<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\Payment;
use App\Models\Domain;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WebsiteController extends Controller
{
    public function index(): View
    {
        $websites = Website::with('payments')->latest()->paginate(10);
        $totalWebsites = Website::count();
        $totalRevenue = Payment::sum('amount');
        $activeWebsites = Website::where('status', 'active')->count();
        
        return view('websites.index', compact('websites', 'totalWebsites', 'totalRevenue', 'activeWebsites'));
    }

    public function create(): View
    {
        return view('websites.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:websites',
            'host_server' => 'required|string|max:255',
            'deployment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
        ]);

        Website::create($validated);

        return redirect()->route('websites.index')->with('success', 'Website created successfully!');
    }

    public function show(Website $website): View
    {
        $website->load('payments');
        return view('websites.show', compact('website'));
    }

    public function edit(Website $website): View
    {
        return view('websites.edit', compact('website'));
    }

    public function update(Request $request, Website $website): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:websites,domain,' . $website->id,
            'host_server' => 'required|string|max:255',
            'deployment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
        ]);

        $website->update($validated);

        return redirect()->route('websites.index')->with('success', 'Website updated successfully!');
    }

    public function destroy(Website $website): RedirectResponse
    {
        $website->delete();

        return redirect()->route('websites.index')->with('success', 'Website deleted successfully!');
    }

    public function dashboard(): View
    {
        $websites = Website::with('payments')->latest()->take(5)->get();
        $domains = Domain::latest()->take(5)->get();
        $emails = Email::latest()->take(5)->get();
        $recentPayments = Payment::with('website')->latest()->take(5)->get();
        
        // Get monthly revenue (current month)
        $currentMonth = now()->startOfMonth();
        $monthlyRevenue = Payment::where('payment_date', '>=', $currentMonth)->sum('amount');
        
        // Get total domain cost
        $totalDomainCost = Domain::sum('annual_cost');
        
        // Get active email plans
        $activeEmailPlans = Email::where('status', 'active')->count();
        
        $stats = [
            'total_websites' => Website::count(),
            'total_revenue' => Payment::sum('amount'),
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
