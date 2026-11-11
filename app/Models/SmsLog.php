<?php

namespace App\Models;

use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use MassPrunable;

    /**
     * Get the prunable model query (Purge SMS delivery logs older than 90 days).
     */
    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(90));
    }
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
