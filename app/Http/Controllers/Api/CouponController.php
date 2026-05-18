<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validate(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string', 'subtotal' => 'required|numeric|min:0']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (! $coupon || ! $coupon->isValid()) {
            return response()->json(['message' => 'Cupom inválido ou expirado.'], 422);
        }

        if ($request->subtotal < $coupon->min_order_value) {
            return response()->json([
                'message' => "Pedido mínimo de R$ " . number_format($coupon->min_order_value, 2, ',', '.') . " para usar este cupom.",
            ], 422);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);

        return response()->json([
            'code'          => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'discount'      => $discount,
            'description'   => $coupon->description,
        ]);
    }
}
