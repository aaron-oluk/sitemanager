<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Website;
use App\Models\Domain;
use App\Models\Email;
use App\Services\CurrencyService;
use App\Services\BillingScheduleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    protected $currencyService;
    protected BillingScheduleService $billingScheduleService;

    public function __construct(CurrencyService $currencyService, BillingScheduleService $billingScheduleService)
    {
        $this->currencyService = $currencyService;
        $this->billingScheduleService = $billingScheduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $payments = Payment::with(['website', 'domain'])->latest()->paginate(15);

        $totalRevenue  = Payment::where('status', 'completed')->sum('usd_equivalent');
        $monthRevenue  = Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('usd_equivalent');
        $totalPayments = Payment::count();

        return view('payments.index', compact('payments', 'totalRevenue', 'monthRevenue', 'totalPayments'));
    }

    public function create(): View
    {
        $currencies = $this->currencyService->getAvailableCurrencies();

        $websites = Website::with(['domainRelation', 'emails'])->orderBy('name')->get();

        $websiteData = $websites->map(function ($w) {
            $items = [];

            $items['hosting'] = [
                'label'     => 'Hosting',
                'total'     => (float) $w->hosting_total_cost,
                'currency'  => $w->currency,
                'breakdown' => [
                    ['label' => 'Hosting base cost',      'amount' => (float) $w->amount_paid],
                    ['label' => 'Tax (18%)',               'amount' => (float) $w->hosting_tax_amount],
                    ['label' => 'Transaction fee (2.5%)',  'amount' => (float) $w->hosting_transaction_fee],
                ],
            ];

            if ($w->domainRelation) {
                $d = $w->domainRelation;
                $items['domain'] = [
                    'id'        => $d->id,
                    'label'     => 'Domain — ' . $d->domain_name,
                    'total'     => (float) $d->renewal_total_cost,
                    'currency'  => 'USD',
                    'breakdown' => [
                        ['label' => 'Registrar cost (base)',  'amount' => (float) $d->annual_cost],
                        ['label' => 'Tax (18%)',              'amount' => (float) $d->renewal_tax_amount],
                        ['label' => 'Transaction fee (2.5%)', 'amount' => (float) $d->renewal_transaction_fee],
                    ],
                ];
            }

            $items['emails'] = $w->emails->map(fn ($e) => [
                'id'        => $e->id,
                'label'     => $e->email_address . ' (' . ucfirst($e->hosting_plan ?? 'monthly') . ')',
                'total'     => (float) $e->billing_total_cost,
                'currency'  => 'USD',
                'breakdown' => [
                    ['label' => 'Subtotal (' . $e->billing_duration_months . ' mo × $' . number_format((float) $e->monthly_cost, 2) . ')', 'amount' => (float) $e->billing_subtotal],
                    ['label' => 'Tax (18%)',              'amount' => (float) $e->billing_tax_amount],
                    ['label' => 'Transaction fee (2.5%)', 'amount' => (float) $e->billing_transaction_fee],
                ],
            ])->values()->all();

            return [
                'id'       => $w->id,
                'label'    => $w->name . ($w->domain ? ' (' . $w->domain . ')' : ''),
                'currency' => $w->currency,
                'items'    => $items,
            ];
        })->values()->all();

        return view('payments.create', compact('currencies', 'websiteData'));
    }

    public function store(Request $request): RedirectResponse
    {
        $currencyList = implode(',', $this->currencyService->getAvailableCurrencies());

        $validated = $request->validate([
            'website_id'       => 'required|exists:websites,id',
            'selected_items'   => 'required|array|min:1',
            'selected_items.*' => 'required|string',
            'amount'           => 'required|numeric|min:0',
            'currency'         => 'required|string|in:' . $currencyList,
            'payment_method'   => 'required|string|max:255',
            'transaction_id'   => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        $website = Website::with(['domainRelation', 'emails'])->findOrFail($validated['website_id']);

        $lineItemRows = [];
        $amountDue    = 0.0;
        $domainId     = null;

        foreach ($validated['selected_items'] as $key) {
            if ($key === 'hosting') {
                $lineItemRows[] = [
                    'item_type'       => 'hosting',
                    'label'           => 'Hosting — ' . $website->name,
                    'unit_cost'       => (float) $website->amount_paid,
                    'tax_amount'      => (float) $website->hosting_tax_amount,
                    'transaction_fee' => (float) $website->hosting_transaction_fee,
                    'total_amount'    => (float) $website->hosting_total_cost,
                    'currency'        => $website->currency,
                    'reference_id'    => $website->id,
                ];
                $amountDue += $this->currencyService->toUSD((float) $website->hosting_total_cost, $website->currency);

            } elseif ($key === 'domain' && $website->domainRelation) {
                $d        = $website->domainRelation;
                $domainId = $d->id;
                $lineItemRows[] = [
                    'item_type'       => 'domain',
                    'label'           => 'Domain renewal — ' . $d->domain_name,
                    'unit_cost'       => (float) $d->annual_cost,
                    'tax_amount'      => (float) $d->renewal_tax_amount,
                    'transaction_fee' => (float) $d->renewal_transaction_fee,
                    'total_amount'    => (float) $d->renewal_total_cost,
                    'currency'        => 'USD',
                    'reference_id'    => $d->id,
                ];
                $amountDue += (float) $d->renewal_total_cost;

            } elseif (str_starts_with($key, 'email:')) {
                $emailId = (int) str_replace('email:', '', $key);
                $email   = $website->emails->find($emailId);
                if ($email) {
                    $lineItemRows[] = [
                        'item_type'       => 'email',
                        'label'           => $email->email_address . ' (' . ucfirst($email->hosting_plan ?? 'monthly') . ')',
                        'unit_cost'       => (float) $email->billing_subtotal,
                        'tax_amount'      => (float) $email->billing_tax_amount,
                        'transaction_fee' => (float) $email->billing_transaction_fee,
                        'total_amount'    => (float) $email->billing_total_cost,
                        'currency'        => 'USD',
                        'reference_id'    => $email->id,
                    ];
                    $amountDue += (float) $email->billing_total_cost;
                }
            }
        }

        $amountPaid    = (float) $validated['amount'];
        $usdEquivalent = $this->currencyService->toUSD($amountPaid, $validated['currency']);
        $status        = ($amountDue > 0 && $usdEquivalent >= $amountDue) ? 'completed' : 'pending';

        $payment = Payment::create([
            'payment_type'   => 'website',
            'website_id'     => $website->id,
            'domain_id'      => $domainId,
            'email_id'       => null,
            'amount'         => $amountPaid,
            'amount_due'     => $amountDue,
            'currency'       => $validated['currency'],
            'usd_equivalent' => $usdEquivalent,
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'payment_date'   => $this->billingScheduleService->now()->toDateString(),
            'receipt_number' => 'RCT-' . strtoupper(uniqid()),
            'status'         => $status,
        ]);

        foreach ($lineItemRows as $row) {
            $payment->lineItems()->create($row);
        }

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['website', 'domain', 'email', 'lineItems']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $payment->load(['website', 'domain', 'email']);
        $currencies = $this->currencyService->getAvailableCurrencies();
        return view('payments.edit', compact('payment', 'currencies'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $currencyList = implode(',', $this->currencyService->getAvailableCurrencies());

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0',
            'amount_due'     => 'nullable|numeric|min:0',
            'currency'       => 'required|string|in:' . $currencyList,
            'payment_method' => 'required|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        // Recalculate status from paid vs due
        $amountDue  = (float) ($validated['amount_due'] ?? $payment->amount_due ?? 0);
        $amountPaid = (float) $validated['amount'];
        $validated['status'] = ($amountDue > 0 && $amountPaid >= $amountDue) ? 'completed' : 'pending';

        $validated['usd_equivalent'] = $this->currencyService->toUSD($amountPaid, $validated['currency']);

        $payment->update($validated);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully!');
    }

    public function viewReceipt(Payment $payment): View
    {
        $payment->load('website');
        return view('payments.receipt', compact('payment'));
    }

    public function generateReceipt(Payment $payment)
    {
        $payment->load('website');
        $pdf = Pdf::loadView('payments.receipt', ['payment' => $payment]);
        $filename = 'receipt-' . ($payment->receipt_number ?? $payment->id) . '.pdf';
        return $pdf->download($filename);
    }
}
