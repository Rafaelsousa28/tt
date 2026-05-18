<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private string $accessToken;
    private string $baseUrl = 'https://api.mercadopago.com';

    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token');
    }

    public function createPreference(Order $order): array
    {
        $items = $order->items->map(fn($item) => [
            'id'          => (string) $item->product_id,
            'title'       => $item->product_name,
            'quantity'    => $item->quantity,
            'unit_price'  => (float) $item->unit_price,
            'currency_id' => 'BRL',
        ])->toArray();

        $payload = [
            'items'    => $items,
            'payer'    => [
                'name'  => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => ['number' => $order->customer_phone],
            ],
            'back_urls' => [
                'success' => config('app.url') . '/pedido/' . $order->order_number . '?status=approved',
                'failure' => config('app.url') . '/checkout?status=failed&order=' . $order->order_number,
                'pending' => config('app.url') . '/pedido/' . $order->order_number . '?status=pending',
            ],
            'auto_return'           => 'approved',
            'notification_url'      => config('app.url') . '/api/payments/webhook',
            'external_reference'    => $order->order_number,
            'statement_descriptor'  => config('app.name'),
            'metadata'              => ['order_number' => $order->order_number],
        ];

        if ($order->shipping_cost > 0) {
            $payload['shipments'] = [
                'cost'           => (float) $order->shipping_cost,
                'mode'           => 'not_specified',
            ];
        }

        if ($order->discount > 0) {
            $payload['coupon_amount'] = (float) $order->discount;
        }

        $response = Http::withToken($this->accessToken)
            ->post("{$this->baseUrl}/checkout/preferences", $payload);

        if ($response->failed()) {
            Log::error('MercadoPago preference error', ['response' => $response->json()]);
            throw new \Exception('Falha ao criar preferência de pagamento.');
        }

        return $response->json();
    }

    public function processWebhook(array $data): void
    {
        if (($data['type'] ?? '') !== 'payment') return;

        $paymentId = $data['data']['id'] ?? null;
        if (! $paymentId) return;

        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}/v1/payments/{$paymentId}");

        if ($response->failed()) return;

        $payment = $response->json();
        $orderNumber = $payment['external_reference'] ?? null;
        if (! $orderNumber) return;

        $order = Order::where('order_number', $orderNumber)->first();
        if (! $order) return;

        $status = match ($payment['status'] ?? '') {
            'approved'   => 'paid',
            'pending'    => 'awaiting_payment',
            'in_process' => 'awaiting_payment',
            'rejected'   => 'cancelled',
            'cancelled'  => 'cancelled',
            'refunded'   => 'refunded',
            default      => $order->status,
        };

        $order->update([
            'status'           => $status,
            'payment_id'       => (string) $paymentId,
            'payment_method'   => $payment['payment_type_id'] ?? null,
            'paid_at'          => $status === 'paid' ? now() : $order->paid_at,
        ]);

        if ($status === 'paid') {
            foreach ($order->items as $item) {
                $item->product?->decrement('stock', $item->quantity);
            }
        }
    }
}
