<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Payment;
use App\Models\Domain;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WebsiteController extends Controller
{
    public function index(): View
    {
        $websites = Website::with(['payments', 'domainRelation'])->latest()->paginate(10);
        $totalWebsites = Website::count();
        $totalRevenue = Payment::sum('usd_equivalent'); // Use USD equivalent
        $activeWebsites = Website::where('status', 'active')->count();
        
        return view('websites.index', compact('websites', 'totalWebsites', 'totalRevenue', 'activeWebsites'));
    }

    public function create(): View
    {
        $domains = Domain::orderBy('domain_name')->pluck('domain_name', 'id');
        $currencies = app(\App\Services\CurrencyService::class)->getAvailableCurrencies();
        return view('websites.create', compact('domains', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Debug: Log the incoming data
        Log::info('Website creation request data:', $request->all());
        
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

        // Debug: Log the validated data
        Log::info('Validated data:', $validated);
        Log::info('Deployment date type: ' . gettype($validated['deployment_date']));
        Log::info('Deployment date value: ' . $validated['deployment_date']);

        // Ensure deployment_date is a Carbon instance
        $validated['deployment_date'] = Carbon::parse($validated['deployment_date']);

        $validated['domain_purchased'] = $request->boolean('domain_purchased');
        $validated['amount_includes_domain'] = $request->boolean('amount_includes_domain');
        $domainCost = $this->calculateDomainCostBreakdown(
            $validated['domain_purchased'] ? (float) ($validated['domain_base_cost'] ?? 0) : 0
        );
        $validated = array_merge($validated, $domainCost);

        $domain = null;

        // If domain_id is provided, link to existing domain
        // If not, create a new domain record
        if (!empty($validated['domain_id'])) {
            $domain = Domain::find($validated['domain_id']);
            if ($domain) {
                $validated['domain'] = $domain->domain_name;
            }
        } else {
            // Create new domain if not linked to existing one
            try {
                Log::info('Creating new domain for website');
                Log::info('Domain name: ' . $validated['domain']);
                Log::info('Deployment date: ' . $validated['deployment_date']->format('Y-m-d'));
                
                $expiryDate = $validated['deployment_date']->copy()->addYear();
                Log::info('Calculated expiry date: ' . $expiryDate->format('Y-m-d'));
                
                $domain = Domain::create([
                    'domain_name' => $validated['domain'],
                    'registrar' => 'Unknown',
                    'registration_date' => $validated['deployment_date'],
                    'expiry_date' => $expiryDate,
                    'annual_cost' => 0,
                    'status' => 'active',
                    'notes' => 'Auto-created from website registration',
                ]);
                
                Log::info('Domain created successfully with ID: ' . $domain->id);
                $validated['domain_id'] = $domain->id;
            } catch (\Exception $e) {
                Log::error('Domain creation failed: ' . $e->getMessage());
                Log::error('Deployment date: ' . $validated['deployment_date']->format('Y-m-d'));
                Log::error('Stack trace: ' . $e->getTraceAsString());
                throw new \Exception('Failed to create domain: ' . $e->getMessage());
            }
        }

        $website = Website::create($validated);

        // Link website to domain in pivot table
        if ($domain) {
            try {
                $website->domains()->attach($domain->id, ['is_primary' => true]);
            } catch (\Exception $e) {
                Log::error('Failed to link website to domain: ' . $e->getMessage());
                // Continue anyway as the website was created
            }
        }

        if ($website->amount_paid > 0) {
            $usdEquivalent = app(\App\Services\CurrencyService::class)->toUSD(
                (float) $website->amount_paid,
                $website->currency
            );
            Payment::create([
                'website_id'     => $website->id,
                'payment_type'   => 'website',
                'amount'         => $website->amount_paid,
                'currency'       => $website->currency,
                'usd_equivalent' => $usdEquivalent,
                'payment_method' => 'Auto-recorded',
                'payment_date'   => $website->deployment_date,
                'status'         => 'completed',
                'notes'          => 'Website: ' . $website->name,
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

    /**
     * Update the specified resource in storage.
     */
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
        $domainCost = $this->calculateDomainCostBreakdown(
            $validated['domain_purchased'] ? (float) ($validated['domain_base_cost'] ?? 0) : 0
        );
        $validated = array_merge($validated, $domainCost);

        // Handle domain relationship
        if (!empty($validated['domain_id'])) {
            $domain = Domain::find($validated['domain_id']);
            if ($domain) {
                $validated['domain'] = $domain->domain_name;
                
                // Update pivot table
                $website->domains()->sync([$domain->id => ['is_primary' => true]]);
            }
        } else {
            // Remove domain relationship
            $website->domains()->detach();
            $validated['domain_id'] = null;
        }

        $website->update($validated);

        return redirect()->route('websites.index')->with('success', 'Website updated successfully!');
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

        $taxAmount = $baseCost * 0.18;
        $transactionFee = $baseCost * 0.025;
        $totalWithCharges = ceil($baseCost + $taxAmount + $transactionFee);

        return [
            'domain_base_cost' => round($baseCost, 2),
            'domain_tax_amount' => round($taxAmount, 2),
            'domain_transaction_fee' => round($transactionFee, 2),
            'domain_total_cost' => round($totalWithCharges, 2),
        ];
    }

    public function destroy(Website $website): RedirectResponse
    {
        $website->delete();

        return redirect()->route('websites.index')->with('success', 'Website deleted successfully!');
    }

    public function dashboard(): View
    {
        $websites = Website::with(['payments', 'domainRelation'])->latest()->take(5)->get();
        $domains = Domain::latest()->take(5)->get();
        $emails = Email::latest()->take(5)->get();
        $recentPayments = Payment::with('website')->latest()->take(5)->get();
        
        // Get monthly revenue (current month) - use USD equivalent
        $currentMonth = now()->startOfMonth();
        $monthlyRevenue = Payment::where('payment_date', '>=', $currentMonth)->sum('usd_equivalent');
        
        // Get total revenue in USD (using usd_equivalent field)
        $totalRevenue = Payment::sum('usd_equivalent');
        
        // Get total domain cost
        $totalDomainCost = Domain::sum('annual_cost');
        
        // Get active email plans
        $activeEmailPlans = Email::where('status', 'active')->count();
        
        $stats = [
            'total_websites' => Website::count(),
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'active_websites' => Website::where('status', 'active')->count(),
            'total_domains' => Domain::count(),
            'expiring_domains' => Domain::where('expiry_date', '<=', now()->addDays(30))->count(),
            'total_emails' => Email::count(),
            'monthly_email_cost' => Email::sum('monthly_cost'),
            'total_domain_cost' => $totalDomainCost,
            'active_email_plans' => $activeEmailPlans,
        ];

        return view('dashboard', compact('websites', 'domains', 'emails', 'recentPayments', 'stats'));
    }
}