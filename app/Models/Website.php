<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'host_server',
        'deployment_date',
        'amount_paid',
        'currency',
        'status',
        'description',
        'client_name',
        'client_email',
        'domain_id', // Add domain relationship
    ];

    protected $casts = [
        'deployment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the domain associated with this website
     */
    public function domainRelation()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    /**
     * Get the domain associated with this website (alias for backward compatibility)
     */
    public function domain()
    {
        return $this->domainRelation();
    }

    /**
     * Get the primary domain name (prioritizes relationship over field)
     */
    public function getPrimaryDomainAttribute(): ?string
    {
        if ($this->domain_id && $this->relationLoaded('domainRelation') && $this->domainRelation) {
            return $this->domainRelation->domain_name;
        }
        return $this->getAttribute('domain');
    }

    /**
     * Get the domain name safely (handles both relationship and field)
     */
    public function getDomainNameAttribute(): ?string
    {
        if ($this->domain_id && $this->relationLoaded('domainRelation') && $this->domainRelation) {
            return $this->domainRelation->domain_name;
        }
        return $this->getAttribute('domain');
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
            return $symbol . number_format($this->amount_paid, 0);
        }
        
        return $symbol . number_format($this->amount_paid, 2);
    }

    /**
     * Get USD equivalent of amount paid
     */
    public function getUsdEquivalentAttribute(): float
    {
        if ($this->currency === 'USD') {
            return $this->amount_paid;
        }

        // Use the same exchange rates as the CurrencyService
        $exchangeRates = [
            'UGX' => 0.00027,
            'EUR' => 1.08,
            'GBP' => 1.26,
            'KES' => 0.0069,
            'TZS' => 0.00039,
            'NGN' => 0.00066,
        ];

        $rate = $exchangeRates[$this->currency] ?? 1.0;
        return $this->amount_paid * $rate;
    }

    /**
     * Get formatted USD equivalent
     */
    public function getFormattedUsdEquivalentAttribute(): string
    {
        return '$' . number_format($this->usd_equivalent, 2);
    }

    /**
     * Get all domains associated with this website (in case of multiple domains)
     */
    public function domains()
    {
        return $this->belongsToMany(Domain::class, 'website_domains');
    }

    /**
     * Check if website has an associated domain
     */
    public function hasDomain(): bool
    {
        return $this->domain_id !== null || $this->domains()->exists();
    }
}
