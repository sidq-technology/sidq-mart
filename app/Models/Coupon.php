<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_spend',
        'expiry_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function isValidForSubtotal($subtotal): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }

        if ($this->min_spend > 0 && $subtotal < $this->min_spend) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($subtotal): float
    {
        if (!$this->isValidForSubtotal($subtotal)) {
            return 0.00;
        }

        if ($this->type === 'percent') {
            return round(($subtotal * ($this->value / 100)), 2);
        }

        return min((float) $this->value, (float) $subtotal);
    }
}
