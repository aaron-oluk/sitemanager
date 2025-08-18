<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_currency',
        'target_currency',
        'rate',
        'last_updated',
        'source',
        'raw_data',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the latest rate for a specific currency pair
     */
    public static function getLatestRate(string $baseCurrency, string $targetCurrency): ?self
    {
        return static::where('base_currency', $baseCurrency)
            ->where('target_currency', $targetCurrency)
            ->latest('last_updated')
            ->first();
    }

    /**
     * Get all latest rates for a base currency
     */
    public static function getLatestRates(string $baseCurrency = 'USD'): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('base_currency', $baseCurrency)
            ->whereIn('id', function($query) use ($baseCurrency) {
                $query->selectRaw('MAX(id)')
                    ->from('currency_rates')
                    ->where('base_currency', $baseCurrency)
                    ->groupBy('target_currency');
            })
            ->get();
    }

    /**
     * Check if rate is stale (older than 1 hour)
     */
    public function isStale(): bool
    {
        return $this->last_updated->diffInHours(now()) >= 1;
    }

    /**
     * Get formatted rate
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 6);
    }
}
