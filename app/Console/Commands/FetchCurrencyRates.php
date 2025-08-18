<?php

namespace App\Console\Commands;

use App\Models\CurrencyRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:fetch-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest currency exchange rates from external APIs';

    /**
     * Supported currencies
     */
    protected array $currencies = [
        'USD', 'UGX', 'EUR', 'GBP', 'KES', 'TZS', 'NGN'
    ];

    /**
     * API endpoints to try (in order of preference)
     */
    protected array $apiEndpoints = [
        'exchangerate-api' => 'https://api.exchangerate-api.com/v4/latest/USD',
        'fixer' => 'http://data.fixer.io/api/latest?access_key=YOUR_API_KEY&base=USD',
        'currencylayer' => 'http://api.currencylayer.com/live?access_key=YOUR_API_KEY&source=USD',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching currency exchange rates...');

        try {
            $rates = $this->fetchRatesFromAPI();
            
            if ($rates) {
                $this->storeRates($rates);
                $this->info('Successfully updated ' . count($rates) . ' currency rates!');
            } else {
                $this->error('Failed to fetch rates from all APIs');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error fetching currency rates: ' . $e->getMessage());
            Log::error('Currency rate fetch failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Try to fetch rates from available APIs
     */
    protected function fetchRatesFromAPI(): ?array
    {
        // Try exchangerate-api first (free tier available)
        $rates = $this->fetchFromExchangerateAPI();
        if ($rates) {
            return $rates;
        }

        // Try fixer.io (requires API key)
        $rates = $this->fetchFromFixer();
        if ($rates) {
            return $rates;
        }

        // Try currencylayer (requires API key)
        $rates = $this->fetchFromCurrencyLayer();
        if ($rates) {
            return $rates;
        }

        // Fallback to default rates if all APIs fail
        $this->warn('All APIs failed, using fallback rates');
        return $this->getFallbackRates();
    }

    /**
     * Fetch from exchangerate-api.com (free tier)
     */
    protected function fetchFromExchangerateAPI(): ?array
    {
        try {
            $response = Http::timeout(10)->get($this->apiEndpoints['exchangerate-api']);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates'])) {
                    $rates = [];
                    foreach ($this->currencies as $currency) {
                        if ($currency === 'USD') {
                            $rates[$currency] = 1.0;
                        } elseif (isset($data['rates'][$currency])) {
                            $rates[$currency] = $data['rates'][$currency];
                        }
                    }
                    
                    $this->info('Successfully fetched rates from exchangerate-api.com');
                    return $rates;
                }
            }
        } catch (\Exception $e) {
            $this->warn('exchangerate-api failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Fetch from fixer.io (requires API key)
     */
    protected function fetchFromFixer(): ?array
    {
        // This would require an API key from fixer.io
        // For now, return null to try next API
        return null;
    }

    /**
     * Fetch from currencylayer (requires API key)
     */
    protected function fetchFromCurrencyLayer(): ?array
    {
        // This would require an API key from currencylayer
        // For now, return null to try next API
        return null;
    }

    /**
     * Get fallback rates (approximate)
     */
    protected function getFallbackRates(): array
    {
        return [
            'USD' => 1.0,
            'UGX' => 3700.0, // 1 USD = ~3700 UGX
            'EUR' => 0.92,    // 1 USD = ~0.92 EUR
            'GBP' => 0.79,    // 1 USD = ~0.79 GBP
            'KES' => 145.0,   // 1 USD = ~145 KES
            'TZS' => 2500.0,  // 1 USD = ~2500 TZS
            'NGN' => 1500.0,  // 1 USD = ~1500 NGN
        ];
    }

    /**
     * Store rates in database
     */
    protected function storeRates(array $rates): void
    {
        $timestamp = now();
        
        foreach ($rates as $currency => $rate) {
            if ($currency === 'USD') {
                continue; // Skip USD as it's the base currency
            }

            // Convert rate to USD base (if API returns inverse)
            $usdRate = 1 / $rate;

            CurrencyRate::updateOrCreate(
                [
                    'base_currency' => 'USD',
                    'target_currency' => $currency,
                ],
                [
                    'rate' => $usdRate,
                    'last_updated' => $timestamp,
                    'source' => 'exchangerate-api',
                    'raw_data' => json_encode(['rate' => $rate, 'usd_rate' => $usdRate]),
                ]
            );
        }
    }
}
