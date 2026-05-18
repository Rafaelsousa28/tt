<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'subtitle', 'button_text', 'button_url',
        'image', 'mobile_image', 'type', 'is_active', 'sort_order',
        'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }

    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function scopeHero($query)
    {
        return $query->where('type', 'hero');
    }

    public function scopePromo($query)
    {
        return $query->where('type', 'promo');
    }
}
