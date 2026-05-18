<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private MercadoPagoService $mp) {}

    public function webhook(Request $request): Response
    {
        try {
            $this->mp->processWebhook($request->all());
        } catch (\Throwable $e) {
            Log::error('Webhook processing error', ['error' => $e->getMessage()]);
        }

        return response('', 200);
    }
}
