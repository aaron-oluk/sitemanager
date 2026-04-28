<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DomainController extends Controller
{
    private const DURATION_OPTIONS = [
        12 => '12 months (1 year)',
        24 => '24 months (2 years)',
        36 => '36 months (3 years)',
    ];

    public function index(): View
    {
        $domains = Domain::latest()->paginate(15);
        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        return view('domains.create', [
            'durationOptions' => self::DURATION_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:domains',
            'registrar' => 'required|string|max:255',
            'registration_date' => 'required|date',
            'subscription_duration_months' => 'required|integer|in:12,24,36',
            'annual_cost' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        $validated['expiry_date'] = $this->calculateExpiryDate(
            $validated['registration_date'],
            (int) $validated['subscription_duration_months']
        );

        unset($validated['subscription_duration_months']);

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
        return view('domains.edit', [
            'domain' => $domain,
            'durationOptions' => self::DURATION_OPTIONS,
            'selectedDurationMonths' => $this->getDurationMonths($domain),
        ]);
    }

    public function update(Request $request, Domain $domain): RedirectResponse
    {
        $validated = $request->validate([
            'domain_name' => 'required|string|max:255|unique:domains,domain_name,' . $domain->id,
            'registrar' => 'required|string|max:255',
            'registration_date' => 'required|date',
            'subscription_duration_months' => 'required|integer|in:12,24,36',
            'annual_cost' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,pending',
            'notes' => 'nullable|string',
        ]);

        $validated['expiry_date'] = $this->calculateExpiryDate(
            $validated['registration_date'],
            (int) $validated['subscription_duration_months']
        );

        unset($validated['subscription_duration_months']);

        $domain->update($validated);

        return redirect()->route('domains.index')->with('success', 'Domain updated successfully!');
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $domain->delete();

        return redirect()->route('domains.index')->with('success', 'Domain deleted successfully!');
    }

    private function calculateExpiryDate(string $registrationDate, int $durationMonths): string
    {
        return Carbon::parse($registrationDate)
            ->addMonthsNoOverflow($durationMonths)
            ->format('Y-m-d');
    }

    private function getDurationMonths(Domain $domain): int
    {
        if (!$domain->registration_date || !$domain->expiry_date) {
            return 12;
        }

        $months = max(1, (int) $domain->registration_date->diffInMonths($domain->expiry_date, false));

        if (in_array($months, array_keys(self::DURATION_OPTIONS), true)) {
            return $months;
        }

        $supportedDurations = array_keys(self::DURATION_OPTIONS);
        $closestDuration = 12;
        $closestDistance = PHP_INT_MAX;

        foreach ($supportedDurations as $supportedDuration) {
            $distance = abs($supportedDuration - $months);

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestDuration = $supportedDuration;
            }
        }

        return $closestDuration;
    }
}
