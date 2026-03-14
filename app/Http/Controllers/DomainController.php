<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DomainController extends Controller
{
    public function index(): View
    {
        $domains = Domain::latest()->paginate(15);
        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        return view('domains.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:domains',
            'registrar' => 'required|string|max:255',
            'registration_date' => 'required|date',
            'expiry_date' => 'required|date|after:registration_date',
            'annual_cost' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        $domain = Domain::create($validated);

        if ($domain->annual_cost > 0) {
            Payment::create([
                'domain_id'      => $domain->id,
                'payment_type'   => 'domain',
                'amount'         => $domain->annual_cost,
                'currency'       => 'USD',
                'usd_equivalent' => $domain->annual_cost,
                'payment_method' => 'Auto-recorded',
                'payment_date'   => $domain->registration_date,
                'status'         => 'completed',
                'notes'          => 'Domain registration: ' . $domain->domain_name,
                'receipt_number' => 'RCT-' . strtoupper(uniqid()),
            ]);
        }

        return redirect()->route('domains.index')->with('success', 'Domain registered successfully!');
    }

    public function show(Domain $domain): View
    {
        return view('domains.show', compact('domain'));
    }

    public function edit(Domain $domain): View
    {
        return view('domains.edit', compact('domain'));
    }

    public function update(Request $request, Domain $domain): RedirectResponse
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:domains,domain_name,' . $domain->id,
            'registrar' => 'required|string|max:255',
            'registration_date' => 'required|date',
            'expiry_date' => 'required|date|after:registration_date',
            'annual_cost' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        $domain->update($validated);

        return redirect()->route('domains.index')->with('success', 'Domain updated successfully!');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domain deleted successfully!');
    }
}
