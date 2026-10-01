<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'delivery_zone',
        'payment_method',
        'customer_note',
        'cart_items',
        'subtotal',
        'shipping_charge',
        'total_amount',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
        'is_recovered',
        'recovered_order_id',
        'contact_notes',
    ];

    protected function casts(): array
    {
        return [
            'cart_items' => 'array',
            'is_recovered' => 'boolean',
            'subtotal' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function recoveredOrder()
    {
        return $this->belongsTo(Order::class, 'recovered_order_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'attempted' => [
                'bg' => 'rgba(239, 68, 68, 0.12)',
                'color' => '#dc2626',
                'border' => '#fca5a5',
                'icon' => 'fa-times-circle',
                'label' => 'প্লেস ব্যর্থ (Attempted)',
            ],
            'contacted' => [
                'bg' => 'rgba(59, 130, 246, 0.12)',
                'color' => '#2563eb',
                'border' => '#bfdbfe',
                'icon' => 'fa-phone',
                'label' => 'যোগাযোগ সম্পন্ন',
            ],
            'recovered' => [
                'bg' => 'rgba(16, 185, 129, 0.12)',
                'color' => '#059669',
                'border' => '#a7f3d0',
                'icon' => 'fa-check-circle',
                'label' => 'অর্ডারে রূপান্তরিত',
            ],
            default => [
                'bg' => 'rgba(245, 158, 11, 0.12)',
                'color' => '#d97706',
                'border' => '#fde68a',
                'icon' => 'fa-clock',
                'label' => 'পরিত্যক্ত (Abandoned)',
            ],
        };
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        if (empty($this->customer_phone)) {
            return null;
        }

        $clean = preg_replace('/[^\d]/', '', $this->customer_phone);
        if (str_starts_with($clean, '01')) {
            $clean = '88' . $clean;
        }

        $siteName = Setting::get('site_name', 'SIDQ MART');
        $name = $this->customer_name ?: 'সম্মানিত গ্রাহক';
        $text = urlencode("আসসালামু আলাইকুম {$name}, {$siteName}-এ আপনার সিলেক্ট করা পছন্দের পণ্যটি অর্ডার সম্পন্ন করতে কোনো সমস্যা হয়েছিল কি? আমরা আপনাকে অর্ডারটি কনফার্ম করতে সাহায্য করতে পারি।");

        return "https://wa.me/{$clean}?text={$text}";
    }
}
