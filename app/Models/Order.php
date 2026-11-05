<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'delivery_zone',
        'shipping_charge',
        'subtotal',
        'discount_amount',
        'grand_total',
        'payment_method',
        'payment_status',
        'transaction_id',
        'order_status',
        'customer_note',
        'ip_address',
        'admin_notes',
        'courier_name',
        'courier_tracking_code',
        'courier_consignment_id',
        'courier_status',
        'courier_submitted_at',
    ];

    protected $casts = [
        'shipping_charge' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'courier_submitted_at' => 'datetime',
    ];

    public function getCourierTrackingUrlAttribute(): ?string
    {
        if (empty($this->courier_tracking_code) && empty($this->courier_consignment_id)) {
            return null;
        }

        $code = $this->courier_tracking_code ?: $this->courier_consignment_id;

        return match (strtolower($this->courier_name ?? '')) {
            'steadfast' => "https://steadfast.com.bd/t/{$code}",
            'pathao' => "https://pathao.com/courier/tracking/?consignment_id={$code}",
            'paperfly' => "https://paperfly.com.bd/tracking?id={$code}",
            'redx' => "https://redx.com.bd/track-order?trackingId={$code}",
            default => null,
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->order_status) {
            'pending' => 'bg-warning text-dark',
            'processing' => 'bg-info text-white',
            'shipped' => 'bg-primary text-white',
            'delivered' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->order_status) {
            'pending' => 'Pending (পেন্ডিং)',
            'processing' => 'Processing (প্রসেসিং)',
            'shipped' => 'Shipped / In Courier (কুরিয়ারে আছে)',
            'delivered' => 'Delivered (ডেলিভার্ড)',
            'cancelled' => 'Cancelled (বাতিল)',
            default => ucfirst($this->order_status),
        };
    }
}
