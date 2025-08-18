<?php

namespace App\Services;

class CurrencyService
{
    // Exchange rates (in a real app, these would come from an API)
    private const EXCHANGE_RATES = [
        'USD' => 1.0,
        'UGX' => 0.00027, // 1 UGX = 0.00027 USD (approximate)
        'EUR' => 1.08,
        'GBP' => 1.26,
        'KES' => 0.0069,
        'TZS' => 0.00039,
        'NGN' => 0.00066,
    ];

    /**
     * Convert amount from one currency to USD
     */
    public function toUSD(float $amount, string $fromCurrency): float
    {
        if ($fromCurrency === 'USD') {
            return $amount;
        }

        $rate = self::EXCHANGE_RATES[$fromCurrency] ?? 1.0;
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

        $rate = self::EXCHANGE_RATES[$toCurrency] ?? 1.0;
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

        // Convert to USD first, then to target currency
        $usdAmount = $this->toUSD($amount, $fromCurrency);
        return $this->fromUSD($usdAmount, $toCurrency);
    }

    /**
     * Get available currencies
     */
    public function getAvailableCurrencies(): array
    {
        return array_keys(self::EXCHANGE_RATES);
    }

    /**
     * Get exchange rate for a currency
     */
    public function getExchangeRate(string $currency): float
    {
        return self::EXCHANGE_RATES[$currency] ?? 1.0;
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
}
