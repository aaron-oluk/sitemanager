<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
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

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbols = [
            'USD' => '$',
            'UGX' => 'USh ',
            'EUR' => '€',
            'GBP' => '£',
            'KES' => 'KSh ',
            'TZS' => 'TSh ',
            'NGN' => '₦',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency . ' ';
        
        if ($this->currency === 'UGX') {
            return $symbol . number_format($this->amount, 0);
        }
        
        return $symbol . number_format($this->amount, 2);
    }

    /**
     * Get USD equivalent formatted
     */
    public function getFormattedUsdEquivalentAttribute(): string
    {
        return '$' . number_format($this->usd_equivalent ?? 0, 2);
    }
}
