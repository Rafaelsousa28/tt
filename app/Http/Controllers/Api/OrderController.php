<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Services\MercadoPagoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private MercadoPagoService $mp) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name'              => 'required|string|max:255',
            'customer_email'             => 'required|email',
            'customer_phone'             => 'required|string',
            'customer_cpf'               => 'nullable|string',
            'shipping_address.street'    => 'required|string',
            'shipping_address.number'    => 'required|string',
            'shipping_address.complement'=> 'nullable|string',
            'shipping_address.district'  => 'required|string',
            'shipping_address.city'      => 'required|string',
            'shipping_address.state'     => 'required|string|size:2',
            'shipping_address.zip_code'  => 'required|string',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity'           => 'required|integer|min:1',
            'coupon_code'                => 'nullable|string',
            'notes'                      => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $items    = collect($data['items']);
            $subtotal = 0;

            $resolvedItems = $items->map(function ($item) use (&$subtotal) {
                $product = Product::active()->lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Estoque insuficiente para '{$product->name}'.");
                }

                $price    = $product->current_price;
                $total    = $price * $item['quantity'];
                $subtotal += $total;

                return [
                    'product'    => $product,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $price,
                    'total'      => $total,
                ];
            });

            $discount = 0;
            $couponCode = null;

            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
                if ($coupon && $coupon->isValid()) {
                    $discount   = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                    $coupon->increment('used_count');
                }
            }

            $shippingCost = $this->calculateShipping($data['shipping_address']['state']);
            $total        = max(0, $subtotal - $discount + $shippingCost);

            $order = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'customer_name'    => $data['customer_name'],
                'customer_email'   => $data['customer_email'],
                'customer_phone'   => $data['customer_phone'],
                'customer_cpf'     => $data['customer_cpf'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'discount'         => $discount,
                'total'            => $total,
                'coupon_code'      => $couponCode,
                'status'           => 'pending',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($resolvedItems as $item) {
                $order->items()->create([
                    'product_id'    => $item['product']->id,
                    'product_name'  => $item['product']->name,
                    'product_sku'   => $item['product']->sku,
                    'product_image' => $item['product']->images[0] ?? null,
                    'unit_price'    => $item['unit_price'],
                    'quantity'      => $item['quantity'],
                    'total'         => $item['total'],
                ]);
            }

            $preference = $this->mp->createPreference($order->load('items'));
            $order->update([
                'mp_preference_id' => $preference['id'],
                'status'           => 'awaiting_payment',
            ]);

            return response()->json([
                'order_number'   => $order->order_number,
                'total'          => $order->total,
                'init_point'     => $preference['init_point'],
                'sandbox_init_point' => $preference['sandbox_init_point'] ?? null,
            ], 201);
        });
    }

    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json(['data' => $order]);
    }

    private function calculateShipping(string $state): float
    {
        $rates = [
            'SP' => 15.00, 'RJ' => 20.00, 'MG' => 18.00, 'ES' => 22.00,
            'RS' => 28.00, 'SC' => 25.00, 'PR' => 22.00,
        ];
        return $rates[$state] ?? 35.00;
    }
}
