<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_name', 'customer_email', 'customer_phone',
        'customer_cpf', 'shipping_address', 'subtotal', 'shipping_cost',
        'discount', 'total', 'coupon_code', 'status', 'payment_method',
        'payment_id', 'mp_preference_id', 'tracking_code', 'notes', 'admin_notes',
        'paid_at', 'shipped_at', 'delivered_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'subtotal'         => 'decimal:2',
        'shipping_cost'    => 'decimal:2',
        'discount'         => 'decimal:2',
        'total'            => 'decimal:2',
        'paid_at'          => 'datetime',
        'shipped_at'       => 'datetime',
        'delivered_at'     => 'datetime',
    ];

    public static function generateOrderNumber(): string
    {
        return 'PLT-' . strtoupper(uniqid());
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'Aguardando',
            'awaiting_payment' => 'Aguardando Pagamento',
            'paid'             => 'Pago',
            'processing'       => 'Em Preparo',
            'shipped'          => 'Enviado',
            'delivered'        => 'Entregue',
            'cancelled'        => 'Cancelado',
            'refunded'         => 'Reembolsado',
            default            => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending', 'awaiting_payment' => 'warning',
            'paid', 'processing'          => 'info',
            'shipped', 'delivered'        => 'success',
            'cancelled', 'refunded'       => 'danger',
            default                       => 'secondary',
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
