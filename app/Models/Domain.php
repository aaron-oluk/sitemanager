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
        'hosting_plan',
        'monthly_cost',
        'quarterly_cost',
        'yearly_cost',
        'status',
        'notes',
        'website_id', // Add website relationship
    ];

    protected $casts = [
        'registration_date' => 'date',
        'expiry_date' => 'date',
        'annual_cost' => 'decimal:2',
        'monthly_cost' => 'decimal:2',
        'quarterly_cost' => 'decimal:2',
        'yearly_cost' => 'decimal:2',
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

    /**
     * Check if domain is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30;
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

    /**
     * Get current hosting plan cost
     */
    public function getCurrentPlanCostAttribute(): float
    {
        switch ($this->hosting_plan) {
            case 'monthly':
                return $this->monthly_cost ?? 0;
            case 'quarterly':
                return $this->quarterly_cost ?? 0;
            case 'yearly':
                return $this->yearly_cost ?? $this->annual_cost ?? 0;
            default:
                return $this->monthly_cost ?? 0;
        }
    }

    /**
     * Get formatted current plan cost
     */
    public function getFormattedCurrentPlanCostAttribute(): string
    {
        $cost = $this->current_plan_cost;
        $period = ucfirst($this->hosting_plan);
        return '$' . number_format($cost, 2) . '/' . $period;
    }

    /**
     * Get hosting plan options
     */
    public static function getHostingPlanOptions(): array
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        ];
    }

    /**
     * Calculate savings from different plans
     */
    public function getPlanSavingsAttribute(): array
    {
        $monthly = $this->monthly_cost ?? 0;
        $quarterly = $this->quarterly_cost ?? 0;
        $yearly = $this->yearly_cost ?? $this->annual_cost ?? 0;

        $savings = [];
        
        if ($quarterly > 0 && $monthly > 0) {
            $quarterlyTotal = $quarterly * 3;
            $monthlyTotal = $monthly * 3;
            $savings['quarterly'] = $monthlyTotal - $quarterlyTotal;
        }
        
        if ($yearly > 0 && $monthly > 0) {
            $yearlyTotal = $yearly;
            $monthlyTotal = $monthly * 12;
            $savings['yearly'] = $monthlyTotal - $yearlyTotal;
        }

        return $savings;
    }
}
