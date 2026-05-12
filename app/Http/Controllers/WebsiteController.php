<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Payment;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class WebsiteController extends Controller
{
    public function index(): View
    {
        $websites = Website::with(['payments', 'domainRelation'])->latest()->paginate(10);
        $totalWebsites = Website::count();
        $totalRevenue = Payment::sum('usd_equivalent');
        $activeWebsites = Website::where('status', 'active')->count();

        return view('websites.index', compact('websites', 'totalWebsites', 'totalRevenue', 'activeWebsites'));
    }

    public function create(): View
    {
        $domains = Domain::orderBy('domain_name')->pluck('domain_name', 'id');
        $currencies = app(\App\Services\CurrencyService::class)->getAvailableCurrencies();
        return view('websites.create', compact('domains', 'currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'domain_id' => 'nullable|exists:domains,id',
            'domain_purchased' => 'nullable|boolean',
            'domain_base_cost' => 'nullable|numeric|min:0|required_if:domain_purchased,1',
            'host_server' => 'required|string|max:255',
            'deployment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'amount_includes_domain' => 'nullable|boolean',
            'currency' => 'required|string|in:' . implode(',', app(\App\Services\CurrencyService::class)->getAvailableCurrencies()),
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
        ]);

        $validated['deployment_date'] = Carbon::parse($validated['deployment_date']);
        $validated['domain_purchased'] = $request->boolean('domain_purchased');
        $validated['amount_includes_domain'] = $request->boolean('amount_includes_domain');

        $domainBaseCost = $validated['domain_purchased'] ? (float) ($validated['domain_base_cost'] ?? 0) : 0;
        $validated = array_merge($validated, $this->calculateDomainCostBreakdown($domainBaseCost));

        $domain = null;
        $domainAnnualCost = $domainBaseCost;

        if (!empty($validated['domain_id'])) {
            $domain = Domain::find($validated['domain_id']);
            if ($domain) {
                $validated['domain'] = $domain->domain_name;
                if ($domainAnnualCost > 0) {
                    $domain->update(['annual_cost' => $domainAnnualCost]);
                }
            }
        } else {
            $expiryDate = $validated['deployment_date']->copy()->addYear();
            $domain = Domain::create([
                'domain_name' => $validated['domain'],
                'registrar' => 'Unknown',
                'registration_date' => $validated['deployment_date'],
                'expiry_date' => $expiryDate,
                'annual_cost' => $domainAnnualCost,
                'status' => 'active',
                'notes' => 'Auto-created from website registration',
            ]);
            $validated['domain_id'] = $domain->id;
        }

        $website = Website::create($validated);

        if ($domain) {
            $website->domains()->attach($domain->id, ['is_primary' => true]);
        }

        if ($website->amount_paid > 0) {
            $usdEquivalent = app(\App\Services\CurrencyService::class)->toUSD(
                (float) $website->amount_paid,
                $website->currency
            );
            Payment::create([
                'website_id' => $website->id,
                'payment_type' => 'website',
                'amount' => $website->amount_paid,
                'currency' => $website->currency,
                'usd_equivalent' => $usdEquivalent,
                'payment_method' => 'Auto-recorded',
                'payment_date' => $website->deployment_date,
                'status' => 'completed',
                'notes' => 'Website: ' . $website->name,
                'receipt_number' => 'RCT-' . strtoupper(uniqid()),
            ]);
        }

        return redirect()->route('websites.index')->with('success', 'Website created successfully!');
    }

    public function show(Website $website): View
    {
        $website->load(['payments', 'domainRelation']);
        return view('websites.show', compact('website'));
    }

    public function edit(Website $website): View
    {
        $domains = Domain::orderBy('domain_name')->pluck('domain_name', 'id');
        $currencies = app(\App\Services\CurrencyService::class)->getAvailableCurrencies();
        return view('websites.edit', compact('website', 'domains', 'currencies'));
    }

    public function update(Request $request, Website $website): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'domain_id' => 'nullable|exists:domains,id',
            'domain_purchased' => 'nullable|boolean',
            'domain_base_cost' => 'nullable|numeric|min:0|required_if:domain_purchased,1',
            'host_server' => 'required|string|max:255',
            'deployment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'amount_includes_domain' => 'nullable|boolean',
            'currency' => 'required|string|in:' . implode(',', app(\App\Services\CurrencyService::class)->getAvailableCurrencies()),
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
        ]);

        $validated['domain_purchased'] = $request->boolean('domain_purchased');
        $validated['amount_includes_domain'] = $request->boolean('amount_includes_domain');

        $domainBaseCost = $validated['domain_purchased'] ? (float) ($validated['domain_base_cost'] ?? 0) : 0;
        $validated = array_merge($validated, $this->calculateDomainCostBreakdown($domainBaseCost));

        if (!empty($validated['domain_id'])) {
            $domain = Domain::find($validated['domain_id']);
            if ($domain) {
                $validated['domain'] = $domain->domain_name;
                if ($domainBaseCost > 0) {
                    $domain->update(['annual_cost' => $domainBaseCost]);
                }
                $website->domains()->sync([$domain->id => ['is_primary' => true]]);
            }
        } else {
            $website->domains()->detach();
            $validated['domain_id'] = null;
        }

        $website->update($validated);

        return redirect()->route('websites.index')->with('success', 'Website updated successfully!');
    }

    public function destroy(Website $website): RedirectResponse
    {
        $website->delete();
        return redirect()->route('websites.index')->with('success', 'Website deleted successfully!');
    }

    private function calculateDomainCostBreakdown(float $baseCost): array
    {
        if ($baseCost <= 0) {
            return [
                'domain_base_cost' => 0,
                'domain_tax_amount' => 0,
                'domain_transaction_fee' => 0,
                'domain_total_cost' => 0,
            ];
        }

        $taxAmount = $baseCost * config('billing.tax_rate');
        $transactionFee = $baseCost * config('billing.transaction_fee_rate');

        return [
            'domain_base_cost' => round($baseCost, 2),
            'domain_tax_amount' => round($taxAmount, 2),
            'domain_transaction_fee' => round($transactionFee, 2),
            'domain_total_cost' => round(ceil($baseCost + $taxAmount + $transactionFee), 2),
        ];
    }
}
