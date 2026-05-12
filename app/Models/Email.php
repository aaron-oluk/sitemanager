<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_address',
        'provider',
        'hosting_plan',
        'monthly_cost',
        'start_date',
        'renewal_date',
        'status',
        'notes',
        'website_id',
        'domain_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'renewal_date' => 'date',
        'monthly_cost' => 'decimal:2',
    ];

    /**
     * Get the domain associated with this email
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    /**
     * Get the website associated with this email
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Extract domain from email address
     */
    public function getDomainFromEmailAttribute(): string
    {
        $parts = explode('@', $this->email_address);
        return end($parts);
    }

    /**
     * Get username part of email
     */
    public function getUsernameAttribute(): string
    {
        $parts = explode('@', $this->email_address);
        return $parts[0];
    }

    public function isExpiringSoon(): bool
    {
        return $this->renewal_date
            && $this->renewal_date->isFuture()
            && now()->diffInDays($this->renewal_date) <= 30;
    }

    /**
     * Get days until renewal
     */
    public function getDaysUntilRenewalAttribute(): ?int
    {
        if (!$this->renewal_date) {
            return null;
        }
        return $this->renewal_date->diffInDays(now(), false);
    }

    /**
     * Get formatted monthly cost
     */
    public function getFormattedMonthlyCostAttribute(): string
    {
        return '$' . number_format($this->monthly_cost, 2);
    }

    public function getBillingDurationMonthsAttribute(): int
    {
        return app(\App\Services\BillingScheduleService::class)
            ->durationMonthsForHostingPlan($this->hosting_plan ?? 'monthly');
    }

    public function getBillingSubtotalAttribute(): float
    {
        return round(((float) $this->monthly_cost) * $this->billing_duration_months, 2);
    }

    public function getBillingTaxAmountAttribute(): float
    {
        return round($this->billing_subtotal * config('billing.tax_rate'), 2);
    }

    public function getBillingTransactionFeeAttribute(): float
    {
        return round($this->billing_subtotal * config('billing.transaction_fee_rate'), 2);
    }

    public function getBillingTotalCostAttribute(): float
    {
        return round(ceil($this->billing_subtotal + $this->billing_tax_amount + $this->billing_transaction_fee), 2);
    }

    public function getFormattedBillingSubtotalAttribute(): string
    {
        return '$' . number_format($this->billing_subtotal, 2);
    }

    public function getFormattedBillingTaxAmountAttribute(): string
    {
        return '$' . number_format($this->billing_tax_amount, 2);
    }

    public function getFormattedBillingTransactionFeeAttribute(): string
    {
        return '$' . number_format($this->billing_transaction_fee, 2);
    }

    public function getFormattedBillingTotalCostAttribute(): string
    {
        return '$' . number_format($this->billing_total_cost, 2);
    }

    /**
     * Get hosting plan options
     */
    public static function getHostingPlanOptions(): array
    {
        return [
            'monthly' => 'Monthly (1 month)',
            'quarterly' => 'Quarterly (3 months)',
            'biannual' => 'Bi-Annual (6 months)',
            'annual' => 'Annual (12 months)',
            'biennial' => 'Bi-Annual (24 months)',
            'triennial' => 'Tri-Annual (36 months)',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
        ];
    }
}
