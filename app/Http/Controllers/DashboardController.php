<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Email;
use App\Models\Payment;
use App\Models\Website;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $websites = Website::with(['payments', 'domainRelation'])->latest()->take(5)->get();
        $domains = Domain::latest()->take(5)->get();
        $emails = Email::latest()->take(5)->get();
        $recentPayments = Payment::with('website')->latest()->take(5)->get();

        $monthlyRevenue = Payment::where('payment_date', '>=', now()->startOfMonth())->sum('usd_equivalent');
        $totalRevenue = Payment::sum('usd_equivalent');

        $stats = [
            'total_websites' => Website::count(),
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'active_websites' => Website::where('status', 'active')->count(),
            'total_domains' => Domain::count(),
            'expiring_domains' => Domain::where('expiry_date', '>', now())
                ->where('expiry_date', '<=', now()->addDays(30))
                ->count(),
            'total_emails' => Email::count(),
            'monthly_email_cost' => Email::sum('monthly_cost'),
            'total_domain_cost' => Domain::sum('annual_cost'),
            'active_email_plans' => Email::where('status', 'active')->count(),
        ];

        return view('dashboard', compact('websites', 'domains', 'emails', 'recentPayments', 'stats'));
    }
}
