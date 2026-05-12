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
        'email_id',
        'payment_type',
        'amount',
        'amount_due',
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
        'payment_date'   => 'date',
        'amount'         => 'decimal:2',
        'amount_due'     => 'decimal:2',
        'usd_equivalent' => 'decimal:2',
    ];

    public function lineItems()
    {
        return $this->hasMany(PaymentLineItem::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function email()
    {
        return $this->belongsTo(\App\Models\Email::class);
    }

    public function getIsFullyPaidAttribute(): bool
    {
        if ($this->amount_due === null || (float) $this->amount_due <= 0) {
            return true;
        }
        return (float) $this->amount >= (float) $this->amount_due;
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
