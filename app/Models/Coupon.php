<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value',
        'min_order_value', 'max_uses', 'used_count', 'is_active',
        'starts_at', 'expires_at',
    ];

    protected $casts = [
        'discount_value'  => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'is_active'       => 'boolean',
        'starts_at'       => 'datetime',
        'expires_at'      => 'datetime',
    ];

    public function isValid(): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) return false;
        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order_value) return 0;

        if ($this->discount_type === 'percentage') {
            return round($subtotal * ($this->discount_value / 100), 2);
        }
        return min($this->discount_value, $subtotal);
    }
}
