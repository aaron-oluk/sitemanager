<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Domain;
use App\Models\Website;

class EmailController extends Controller
{
    public function index(): View
    {
        // Get emails grouped by domain for clustering
        $emailsByDomain = Email::with(['domain', 'website'])
            ->get()
            ->groupBy('domain_id')
            ->map(function ($emails, $domainId) {
                if ($domainId) {
                    $domain = Domain::find($domainId);
                    return [
                        'domain' => $domain,
                        'emails' => $emails,
                        'total_cost' => $emails->sum('monthly_cost'),
                        'count' => $emails->count()
                    ];
                } else {
                    // Emails without domain association
                    return [
                        'domain' => null,
                        'emails' => $emails,
                        'total_cost' => $emails->sum('monthly_cost'),
                        'count' => $emails->count()
                    ];
                }
            });

        // Also get regular paginated emails for backward compatibility
        $emails = Email::with(['domain', 'website'])->latest()->paginate(15);
        
        $totalEmails = Email::count();
        $totalMonthlyCost = Email::sum('monthly_cost');
        $activeEmails = Email::where('status', 'active')->count();
        
        return view('emails.index', compact('emailsByDomain', 'emails', 'totalEmails', 'totalMonthlyCost', 'activeEmails'));
    }

    public function create(): View
    {
        $domains = Domain::orderBy('domain_name')->pluck('domain_name', 'id');
        $websites = Website::orderBy('name')->pluck('name', 'id');
        $hostingPlans = Email::getHostingPlanOptions();
        $statusOptions = Email::getStatusOptions();
        return view('emails.create', compact('domains', 'websites', 'hostingPlans', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_address' => 'required|email|max:255|unique:emails',
            'provider' => 'required|string|max:255',
            'hosting_plan' => 'required|string|max:255',
            'monthly_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
            'associated_website' => 'nullable|exists:websites,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        // Auto-assign domain if not provided
        if (empty($validated['domain_id'])) {
            $emailParts = explode('@', $validated['email_address']);
            $domainName = end($emailParts);
            
            $domain = Domain::where('domain_name', $domainName)->first();
            if ($domain) {
                $validated['domain_id'] = $domain->id;
            }
        }

        Email::create($validated);

        return redirect()->route('emails.index')->with('success', 'Email account created successfully!');
    }

    public function show(Email $email): View
    {
        return view('emails.show', compact('email'));
    }

    public function edit(Email $email): View
    {
        $domains = Domain::orderBy('domain_name')->pluck('domain_name', 'id');
        $websites = Website::orderBy('name')->pluck('name', 'id');
        return view('emails.edit', compact('email', 'domains', 'websites'));
    }

    public function update(Request $request, Email $email): RedirectResponse
    {
        $validated = $request->validate([
            'email_address' => 'required|email|unique:emails,email_address,' . $email->id,
            'provider' => 'required|string|max:255',
            'hosting_plan' => 'nullable|string|max:255',
            'monthly_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,suspended,pending,cancelled',
            'notes' => 'nullable|string',
            'associated_website' => 'nullable|exists:websites,id',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        // Auto-assign domain if not provided
        if (empty($validated['domain_id'])) {
            $emailParts = explode('@', $validated['email_address']);
            $domainName = end($emailParts);
            
            $domain = Domain::where('domain_name', $domainName)->first();
            if ($domain) {
                $validated['domain_id'] = $domain->id;
            }
        }

        $email->update($validated);

        return redirect()->route('emails.index')->with('success', 'Email account updated successfully!');
    }

    public function destroy(Email $email): RedirectResponse
    {
        $email->delete();

        return redirect()->route('emails.index')->with('success', 'Email account deleted successfully!');
    }
}