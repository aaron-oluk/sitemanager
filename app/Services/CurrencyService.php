<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    // Fallback exchange rates (in case database is empty)
    private const FALLBACK_RATES = [
        'USD' => 1.0,
        'UGX' => 0.00027, // 1 UGX = 0.00027 USD (approximate)
        'EUR' => 1.08,
        'GBP' => 1.26,
        'KES' => 0.0069,
        'TZS' => 0.00039,
        'NGN' => 0.00066,
    ];

    /**
     * Get exchange rate from database or fallback
     */
    public function getExchangeRate(string $currency): float
    {
        if ($currency === 'USD') {
            return 1.0;
        }

        // Try to get from database first
        $rate = $this->getRateFromDatabase($currency);
        
        if ($rate !== null) {
            return $rate;
        }

        // Fallback to static rates
        return self::FALLBACK_RATES[$currency] ?? 1.0;
    }

    /**
     * Get rate from database with caching
     */
    private function getRateFromDatabase(string $currency): ?float
    {
        $cacheKey = "currency_rate_{$currency}";
        
        return Cache::remember($cacheKey, 300, function () use ($currency) { // Cache for 5 minutes
            $rateRecord = CurrencyRate::getLatestRate('USD', $currency);
            
            if ($rateRecord && !$rateRecord->isStale()) {
                return (float) $rateRecord->rate;
            }
            
            return null;
        });
    }

    /**
     * Convert amount from one currency to USD
     */
    public function toUSD(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency);
        return $amount * $rate;
    }

    /**
     * Convert amount from USD to another currency
     */
    public function fromUSD(float $amount, string $toCurrency): float
    {
        if ($toCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->getExchangeRate($toCurrency);
        return $amount / $rate;
    }

    /**
     * Convert amount between two currencies
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Convert to USD equivalent first, then to target currency
        $usdAmount = $this->toUSD($amount, $fromCurrency);
        return $this->fromUSD($usdAmount, $toCurrency);
    }

    /**
     * Get available currencies
     */
    public function getAvailableCurrencies(): array
    {
        return array_keys(self::FALLBACK_RATES);
    }

    /**
     * Format currency amount
     */
    public function formatAmount(float $amount, string $currency): string
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

        $symbol = $symbols[$currency] ?? $currency . ' ';
        
        if ($currency === 'UGX') {
            return $symbol . number_format($amount, 0);
        }
        
        return $symbol . number_format($amount, 2);
    }

    /**
     * Get all current rates from database
     */
    public function getCurrentRates(): array
    {
        $rates = ['USD' => 1.0];
        
        foreach ($this->getAvailableCurrencies() as $currency) {
            if ($currency !== 'USD') {
                $rates[$currency] = $this->getExchangeRate($currency);
            }
        }
        
        return $rates;
    }

    /**
     * Get rate info including last updated time
     */
    public function getRateInfo(string $currency): ?array
    {
        if ($currency === 'USD') {
            return [
                'rate' => 1.0,
                'last_updated' => now(),
                'source' => 'system',
                'is_stale' => false
            ];
        }

        $rateRecord = CurrencyRate::getLatestRate('USD', $currency);
        
        if ($rateRecord) {
            return [
                'rate' => (float) $rateRecord->rate,
                'last_updated' => $rateRecord->last_updated,
                'source' => $rateRecord->source,
                'is_stale' => $rateRecord->isStale()
            ];
        }

        return null;
    }

    /**
     * Check if rates need updating
     */
    public function needsUpdate(): bool
    {
        $currencies = $this->getAvailableCurrencies();
        
        foreach ($currencies as $currency) {
            if ($currency === 'USD') continue;
            
            $rateInfo = $this->getRateInfo($currency);
            if (!$rateInfo || $rateInfo['is_stale']) {
                return true;
            }
        }
        
        return false;
    }
}
