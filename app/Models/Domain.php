<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_name',
        'registrar',
        'registration_date',
        'expiry_date',
        'annual_cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'annual_cost' => 'decimal:2',
    ];

    /**
     * Get the website associated with this domain
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Get all websites associated with this domain (in case of multiple websites)
     */
    public function websites()
    {
        return $this->belongsToMany(Website::class, 'website_domains');
    }

    /**
     * Get all emails associated with this domain
     */
    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date
            && $this->expiry_date->isFuture()
            && now()->diffInDays($this->expiry_date) <= 30;
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return $this->expiry_date->diffInDays(now(), false);
    }

    /**
     * Get formatted annual cost
     */
    public function getFormattedAnnualCostAttribute(): string
    {
        return '$' . number_format($this->annual_cost, 2);
    }

    public function getRenewalTaxAmountAttribute(): float
    {
        return round(((float) $this->annual_cost) * config('billing.tax_rate'), 2);
    }

    public function getRenewalTransactionFeeAttribute(): float
    {
        return round(((float) $this->annual_cost) * config('billing.transaction_fee_rate'), 2);
    }

    public function getRenewalTotalCostAttribute(): float
    {
        return round(ceil((float) $this->annual_cost + $this->renewal_tax_amount + $this->renewal_transaction_fee), 2);
    }

    public function getFormattedRenewalTaxAmountAttribute(): string
    {
        return '$' . number_format($this->renewal_tax_amount, 2);
    }

    public function getFormattedRenewalTransactionFeeAttribute(): string
    {
        return '$' . number_format($this->renewal_transaction_fee, 2);
    }

    public function getFormattedRenewalTotalCostAttribute(): string
    {
        return '$' . number_format($this->renewal_total_cost, 2);
    }
}