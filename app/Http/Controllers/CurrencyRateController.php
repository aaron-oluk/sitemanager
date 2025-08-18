<?php

namespace App\Http\Controllers;

use App\Models\CurrencyRate;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class CurrencyRateController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Display a listing of currency rates
     */
    public function index(): View
    {
        $rates = CurrencyRate::getLatestRates('USD');
        $currentRates = $this->currencyService->getCurrentRates();
        $needsUpdate = $this->currencyService->needsUpdate();
        
        return view('currency-rates.index', compact('rates', 'currentRates', 'needsUpdate'));
    }

    /**
     * Manually refresh currency rates
     */
    public function refresh(): RedirectResponse
    {
        try {
            Artisan::call('currency:fetch-rates');
            $output = Artisan::output();
            
            return redirect()->route('currency-rates.index')
                ->with('success', 'Currency rates refreshed successfully! ' . trim($output));
        } catch (\Exception $e) {
            return redirect()->route('currency-rates.index')
                ->with('error', 'Failed to refresh currency rates: ' . $e->getMessage());
        }
    }

    /**
     * Get current rates as JSON (for AJAX requests)
     */
    public function getRates(): \Illuminate\Http\JsonResponse
    {
        $rates = $this->currencyService->getCurrentRates();
        $lastUpdated = CurrencyRate::max('last_updated');
        
        return response()->json([
            'rates' => $rates,
            'last_updated' => $lastUpdated,
            'needs_update' => $this->currencyService->needsUpdate()
        ]);
    }
}
