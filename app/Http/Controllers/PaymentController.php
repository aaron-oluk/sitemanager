<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Website;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $payments = Payment::with('website')->latest()->paginate(15);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $websites = Website::orderBy('name')->pluck('name', 'id');
        return view('payments.create', compact('websites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'status' => 'required|in:completed,pending,failed',
            'notes' => 'nullable|string',
        ]);

        // Generate receipt number if not provided
        $validated['receipt_number'] = $validated['receipt_number'] ?? 'RCT-' . strtoupper(uniqid());

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $payment->load('website');
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment): View
    {
        $websites = Website::orderBy('name')->pluck('name', 'id');
        return view('payments.edit', compact('payment', 'websites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'status' => 'required|in:completed,pending,failed',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully!');
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
