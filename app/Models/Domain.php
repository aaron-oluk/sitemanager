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
        'website_id', // Add website relationship
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
}