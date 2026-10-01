<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'color',
        'size',
        'product_image',
        'unit_price',
        'quantity',
        'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getVariantTextAttribute(): ?string
    {
        $parts = [];
        if (!empty($this->color)) {
            $parts[] = 'কালার: ' . $this->color;
        }
        if (!empty($this->size)) {
            $parts[] = 'সাইজ: ' . $this->size;
        }
        return !empty($parts) ? implode(' | ', $parts) : null;
    }
}
