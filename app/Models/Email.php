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

    /**
     * Check if email is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->renewal_date && $this->renewal_date->diffInDays(now()) <= 30;
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
