<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'recipient',
        'message',
        'purpose',
        'order_id',
        'status',
        'provider',
        'response_data',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
