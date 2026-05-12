<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'domain_id',
        'payment_type',
        'amount',
        'currency',
        'usd_equivalent',
        'payment_method',
        'transaction_id',
        'payment_date',
        'status',
        'notes',
        'receipt_number',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'usd_equivalent' => 'decimal:2',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return app(\App\Services\CurrencyService::class)
            ->formatAmount((float) $this->amount, $this->currency);
    }

    /**
     * Get USD equivalent formatted
     */
    public function getFormattedUsdEquivalentAttribute(): string
    {
        return '$' . number_format($this->usd_equivalent ?? 0, 2);
    }
}
