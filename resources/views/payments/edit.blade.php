@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('payments.show', $payment) }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Payment</h1>
            @if($payment->receipt_number)
                <p class="text-sm text-gray-500 mt-0.5">{{ $payment->receipt_number }}</p>
            @endif
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- What this payment is for (read-only) --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Payment For</h2>
        </div>
        <div class="px-5 py-4 flex items-center gap-4">
            <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $payment->payment_type === 'domain' ? 'bg-purple-100 text-purple-700' : ($payment->payment_type === 'email' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                {{ ucfirst($payment->payment_type ?? 'website') }}
            </span>
            <span class="text-sm font-medium text-gray-900">
                @if($payment->payment_type === 'domain')
                    {{ $payment->domain->domain_name ?? 'N/A' }}
                @elseif($payment->payment_type === 'email')
                    {{ $payment->email->email_address ?? 'N/A' }}
                @else
                    {{ $payment->website->name ?? 'N/A' }}
                @endif
            </span>
            @if($payment->amount_due)
            <span class="ml-auto text-sm text-gray-500">Amount due: <span class="font-semibold text-gray-900">${{ number_format($payment->amount_due, 2) }}</span></span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('payments.update', $payment) }}" class="space-y-5" id="edit-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="amount_due" value="{{ $payment->amount_due }}" />

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Payment Details</h2>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Amount Paid</label>
                    <input type="number" name="amount" id="amount-input" step="0.01" min="0"
                        value="{{ old('amount', $payment->amount) }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Currency</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($currencies as $c)
                            <option value="{{ $c }}" {{ old('currency', $payment->currency) === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Payment Method</label>
                    <input type="text" name="payment_method" value="{{ old('payment_method', $payment->payment_method) }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Transaction ID <span class="normal-case font-normal">(optional)</span></label>
                    <input type="text" name="transaction_id" value="{{ old('transaction_id', $payment->transaction_id) }}"
                        class="w-full rounded-lg border-gray-300 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes <span class="normal-case font-normal">(optional)</span></label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $payment->notes) }}</textarea>
                </div>
            </div>

            {{-- Live status preview --}}
            @if($payment->amount_due)
            <div class="px-5 pb-5">
                <div id="status-summary" class="rounded-xl border px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" id="status-label-text"></p>
                        <p class="text-sm" id="status-desc-text"></p>
                    </div>
                    <div id="status-icon" class="h-10 w-10 rounded-full flex items-center justify-center shrink-0"></div>
                </div>
            </div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('payments.show', $payment) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">Update Payment</button>
        </div>
    </form>

</div>
@endsection

@section('scripts')
@if($payment->amount_due)
<script>
(function () {
    const amountDue    = {{ (float) $payment->amount_due }};
    const currency     = '{{ $payment->currency }}';
    const amountInput  = document.getElementById('amount-input');
    const statusSummary = document.getElementById('status-summary');
    const statusLabel  = document.getElementById('status-label-text');
    const statusDesc   = document.getElementById('status-desc-text');
    const statusIcon   = document.getElementById('status-icon');

    function fmt(v) {
        const symbols = {USD:'$', UGX:'USh ', EUR:'€', GBP:'£', KES:'KSh ', TZS:'TSh ', NGN:'₦'};
        const sym = symbols[currency] || (currency + ' ');
        const dec = ['UGX','TZS'].includes(currency) ? 0 : 2;
        return sym + parseFloat(v || 0).toFixed(dec);
    }

    function update() {
        const paid = parseFloat(amountInput.value) || 0;
        const full  = paid >= amountDue;
        const bal   = amountDue - paid;

        if (full) {
            statusSummary.className = 'rounded-xl border border-green-200 bg-green-50 px-5 py-4 flex items-center justify-between';
            statusLabel.className = 'text-xs font-semibold uppercase tracking-wider mb-0.5 text-green-700';
            statusLabel.textContent = 'Payment Complete';
            statusDesc.className = 'text-sm text-green-600';
            statusDesc.textContent = 'Full amount covered. Status will be set to Completed.';
            statusIcon.className = 'h-10 w-10 rounded-full bg-green-100 flex items-center justify-center shrink-0';
            statusIcon.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else {
            statusSummary.className = 'rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-center justify-between';
            statusLabel.className = 'text-xs font-semibold uppercase tracking-wider mb-0.5 text-amber-700';
            statusLabel.textContent = 'Partial / Pending';
            statusDesc.className = 'text-sm text-amber-600';
            statusDesc.textContent = fmt(bal) + ' still outstanding. Status will be set to Pending.';
            statusIcon.className = 'h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0';
            statusIcon.innerHTML = '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }
    }

    amountInput.addEventListener('input', update);
    update();
})();
</script>
@endif
@endsection
