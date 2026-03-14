<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\Payment;
use App\Models\Website;
use App\Services\CurrencyService;
use Illuminate\Console\Command;

class BackfillPayments extends Command
{
    protected $signature = 'payments:backfill';
    protected $description = 'Create payment records for existing domains and websites that have no payment yet';

    public function handle(CurrencyService $currencyService): void
    {
        // Domains with a cost but no payment recorded yet
        $domains = Domain::where('annual_cost', '>', 0)
            ->whereNotExists(function ($q) {
                $q->from('payments')->whereColumn('payments.domain_id', 'domains.id');
            })
            ->get();

        foreach ($domains as $domain) {
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
            $this->line("  Domain: {$domain->domain_name} — \${$domain->annual_cost}");
        }

        $this->info("Backfilled {$domains->count()} domain payment(s).");

        // Websites with an amount paid but no payment recorded yet
        $websites = Website::where('amount_paid', '>', 0)
            ->whereNotExists(function ($q) {
                $q->from('payments')->whereColumn('payments.website_id', 'websites.id');
            })
            ->get();

        foreach ($websites as $website) {
            $usdEquivalent = $currencyService->toUSD((float) $website->amount_paid, $website->currency);
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
            $this->line("  Website: {$website->name} — {$website->currency} {$website->amount_paid}");
        }

        $this->info("Backfilled {$websites->count()} website payment(s).");
    }
}
