<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    // Rates expressed as "1 USD = X currency" (conventional direction).
    private const FALLBACK_RATES = [
        'USD' => 1.0,
        'UGX' => 3700.0,
        'EUR' => 0.93,
        'GBP' => 0.79,
        'KES' => 145.0,
        'TZS' => 2564.0,
        'NGN' => 1515.0,
    ];

    public function getExchangeRate(string $currency): float
    {
        if ($currency === 'USD') {
            return 1.0;
        }

        $rate = $this->getRateFromDatabase($currency);
        return $rate ?? self::FALLBACK_RATES[$currency] ?? 1.0;
    }

    private function getRateFromDatabase(string $currency): ?float
    {
        $cacheKey = "currency_rate_{$currency}";

        return Cache::remember($cacheKey, 300, function () use ($currency) {
            $rateRecord = CurrencyRate::getLatestRate('USD', $currency);

            if ($rateRecord && !$rateRecord->isStale()) {
                return (float) $rateRecord->rate;
            }

            return null;
        });
    }

    /**
     * Convert an amount in the given currency to USD.
     * Stored/fallback rate = "1 USD = X currency", so divide to get USD.
     */
    public function toUSD(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency);
        return $rate > 0 ? $amount / $rate : $amount;
    }

    /**
     * Convert an amount from USD to the given currency.
     */
    public function fromUSD(float $amount, string $toCurrency): float
    {
        if ($toCurrency === 'USD') {
            return $amount;
        }

        $rate = $this->getExchangeRate($toCurrency);
        return $amount * $rate;
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        return $this->fromUSD($this->toUSD($amount, $fromCurrency), $toCurrency);
    }

    public function getAvailableCurrencies(): array
    {
        return array_keys(self::FALLBACK_RATES);
    }

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

    public function getRateInfo(string $currency): ?array
    {
        if ($currency === 'USD') {
            return ['rate' => 1.0, 'last_updated' => now(), 'source' => 'system', 'is_stale' => false];
        }

        $rateRecord = CurrencyRate::getLatestRate('USD', $currency);

        if ($rateRecord) {
            return [
                'rate' => (float) $rateRecord->rate,
                'last_updated' => $rateRecord->last_updated,
                'source' => $rateRecord->source,
                'is_stale' => $rateRecord->isStale(),
            ];
        }

        return null;
    }

    public function needsUpdate(): bool
    {
        foreach ($this->getAvailableCurrencies() as $currency) {
            if ($currency === 'USD') {
                continue;
            }

            $rateInfo = $this->getRateInfo($currency);
            if (!$rateInfo || $rateInfo['is_stale']) {
                return true;
            }
        }

        return false;
    }
}
