<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'regular_price',
        'sale_price',
        'stock_quantity',
        'is_featured',
        'is_flash_sale',
        'discount_percent',
        'short_description',
        'description',
        'specifications',
        'primary_image',
        'is_active',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_flash_sale' => 'boolean',
        'is_active' => 'boolean',
        'specifications' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
            if ($product->regular_price && $product->sale_price && $product->regular_price > $product->sale_price) {
                $product->discount_percent = (int) round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100);
            }
        });

        static::updating(function ($product) {
            if ($product->regular_price && $product->sale_price && $product->regular_price > $product->sale_price) {
                $product->discount_percent = (int) round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->regular_price;
    }

    public function getPrimaryImageUrlAttribute()
    {
        if ($this->primary_image && file_exists(public_path('storage/' . $this->primary_image))) {
            return asset('storage/' . $this->primary_image);
        }
        if ($this->primary_image && (str_starts_with($this->primary_image, 'http://') || str_starts_with($this->primary_image, 'https://'))) {
            return $this->primary_image;
        }
        return asset('images/placeholder-product.png');
    }
}
