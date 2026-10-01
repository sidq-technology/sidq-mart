<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'color_code',
        'size',
        'sku',
        'price',
        'stock_quantity',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function getVariantNameAttribute(): string
    {
        $parts = [];
        if (!empty($this->color)) {
            $parts[] = 'কালার: ' . $this->color;
        }
        if (!empty($this->size)) {
            $parts[] = 'সাইজ: ' . $this->size;
        }
        return !empty($parts) ? implode(' | ', $parts) : '';
    }

    public function getShortNameAttribute(): string
    {
        $parts = [];
        if (!empty($this->color)) {
            $parts[] = $this->color;
        }
        if (!empty($this->size)) {
            $parts[] = $this->size;
        }
        return !empty($parts) ? implode(' - ', $parts) : '';
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path && file_exists(public_path('storage/' . $this->image_path))) {
            return asset('storage/' . $this->image_path);
        }
        if ($this->image_path && (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://'))) {
            return $this->image_path;
        }
        return $this->product?->primary_image_url;
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->price !== null && (float)$this->price > 0) {
            return (float) $this->price;
        }
        return (float) ($this->product?->current_price ?? 0);
    }
}
