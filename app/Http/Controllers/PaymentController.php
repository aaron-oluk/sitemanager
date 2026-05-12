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

        $websites = Website::orderBy('name')->get(['id', 'name', 'domain', 'amount_paid', 'currency']);
        $domains  = Domain::orderBy('domain_name')->get(['id', 'domain_name', 'annual_cost']);
        $emails   = Email::orderBy('email_address')->get(['id', 'email_address', 'monthly_cost', 'hosting_plan']);

        // Build lightweight payloads for the JS picker
        $websiteData = $websites->map(fn ($w) => [
            'id'         => $w->id,
            'label'      => $w->name . ' (' . $w->domain . ')',
            'amount_due' => (float) $w->amount_paid,
            'currency'   => $w->currency,
            'breakdown'  => [
                ['label' => 'Hosting fee', 'amount' => (float) $w->amount_paid],
            ],
        ]);

        $domainData = $domains->map(fn ($d) => [
            'id'         => $d->id,
            'label'      => $d->domain_name,
            'amount_due' => (float) $d->renewal_total_cost,
            'currency'   => 'USD',
            'breakdown'  => [
                ['label' => 'Registrar cost (base)',  'amount' => (float) $d->annual_cost],
                ['label' => 'Tax (18%)',              'amount' => (float) $d->renewal_tax_amount],
                ['label' => 'Transaction fee (2.5%)', 'amount' => (float) $d->renewal_transaction_fee],
            ],
        ]);

        $emailData = $emails->map(fn ($e) => [
            'id'         => $e->id,
            'label'      => $e->email_address . ' (' . ucfirst($e->hosting_plan ?? 'monthly') . ')',
            'amount_due' => (float) $e->billing_total_cost,
            'currency'   => 'USD',
            'breakdown'  => [
                ['label' => 'Subtotal (' . $e->billing_duration_months . ' mo × $' . number_format((float) $e->monthly_cost, 2) . ')', 'amount' => (float) $e->billing_subtotal],
                ['label' => 'Tax (18%)',              'amount' => (float) $e->billing_tax_amount],
                ['label' => 'Transaction fee (2.5%)', 'amount' => (float) $e->billing_transaction_fee],
            ],
        ]);

        return view('payments.create', compact('currencies', 'websiteData', 'domainData', 'emailData'));
    }

    public function store(Request $request): RedirectResponse
    {
        $currencyList = implode(',', $this->currencyService->getAvailableCurrencies());

        $validated = $request->validate([
            'payment_type'   => 'required|in:website,domain,email',
            'website_id'     => 'nullable|required_if:payment_type,website|exists:websites,id',
            'domain_id'      => 'nullable|required_if:payment_type,domain|exists:domains,id',
            'email_id'       => 'nullable|required_if:payment_type,email|exists:emails,id',
            'amount_due'     => 'required|numeric|min:0',
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'required|string|in:' . $currencyList,
            'payment_method' => 'required|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        // Auto-determine status from paid vs due
        $amountDue  = (float) $validated['amount_due'];
        $amountPaid = (float) $validated['amount'];
        $validated['status'] = ($amountDue > 0 && $amountPaid >= $amountDue) ? 'completed' : 'pending';

        $validated['payment_date']   = $this->billingScheduleService->now()->toDateString();
        $validated['receipt_number'] = 'RCT-' . strtoupper(uniqid());
        $validated['usd_equivalent'] = $this->currencyService->toUSD($amountPaid, $validated['currency']);

        // Clear unrelated FK columns
        if ($validated['payment_type'] !== 'website') $validated['website_id'] = null;
        if ($validated['payment_type'] !== 'domain')  $validated['domain_id']  = null;
        if ($validated['payment_type'] !== 'email')   $validated['email_id']   = null;

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['website', 'domain', 'email']);
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
