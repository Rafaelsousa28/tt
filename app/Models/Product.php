<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'sku', 'price', 'sale_price', 'stock', 'min_stock_alert',
        'images', 'is_active', 'is_featured', 'care_level',
        'light_requirement', 'water_frequency', 'height_cm', 'pot_size_cm', 'extra_attributes',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'images'           => 'array',
        'extra_attributes' => 'array',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getFirstImageUrlAttribute(): ?string
    {
        $images = $this->images ?? [];
        return count($images) > 0 ? asset('storage/' . $images[0]) : null;
    }

    public function getImageUrlsAttribute(): array
    {
        return array_map(fn($img) => asset('storage/' . $img), $this->images ?? []);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock_alert;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
