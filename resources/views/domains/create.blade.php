@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('domains.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Register Domain</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('domains.store') }}" class="space-y-5">
        @csrf

        {{-- Registration details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Registration Details</h2>
            </div>
            <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Domain Name</label>
                    <input name="domain_name" value="{{ old('domain_name') }}" required placeholder="example.com" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Registrar</label>
                    <input name="registrar" value="{{ old('registrar') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Registration Date</label>
                    <input type="date" name="registration_date" value="{{ old('registration_date') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Subscription Duration</label>
                    <select id="subscription_duration_create" name="subscription_duration_months" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($durationOptions as $months => $label)
                            <option value="{{ $months }}" {{ old('subscription_duration_months', 12) == $months ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Calculated Expiry Date</label>
                    <input id="calculated_expiry_create" type="date" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-600" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Annual Cost ($)</label>
                    <input type="number" step="0.01" name="annual_cost" value="{{ old('annual_cost') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['active','expired','pending'] as $s)
                            <option value="{{ $s }}" {{ old('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Renewal calculator --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Renewal Cost Estimator</h2>
                <p class="text-xs text-gray-500 mt-0.5">Preview what renewal will cost — does not affect saved data.</p>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Renewal Base Cost ($)</label>
                        <input id="renewal_base_cost_create" type="number" step="0.01" min="0" value="{{ old('annual_cost') }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Billing Frequency</label>
                        <select id="renewal_billing_frequency_create" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="annual" selected>Annual</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-100 px-5 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tax (18%)</p>
                            <input id="renewal_tax_preview_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Fee (2.5%)</p>
                            <input id="renewal_txn_preview_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total</p>
                            <input id="renewal_total_preview_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm font-semibold text-gray-900" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">No. of Payments</p>
                            <input id="renewal_payment_count_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Amount per Payment</p>
                            <input id="renewal_payment_amount_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm font-semibold text-gray-900" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('domains.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">Save Domain</button>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
(function () {
    const registrationInput = document.querySelector('input[name="registration_date"]');
    const durationSelect = document.getElementById('subscription_duration_create');
    const expiryPreview = document.getElementById('calculated_expiry_create');
    const baseInput = document.getElementById('renewal_base_cost_create');
    const frequencySelect = document.getElementById('renewal_billing_frequency_create');
    const taxPreview = document.getElementById('renewal_tax_preview_create');
    const txnPreview = document.getElementById('renewal_txn_preview_create');
    const totalPreview = document.getElementById('renewal_total_preview_create');
    const paymentCountPreview = document.getElementById('renewal_payment_count_create');
    const paymentAmountPreview = document.getElementById('renewal_payment_amount_create');

    function formatMoney(value) { return '$' + Number(value || 0).toFixed(2); }

    function updateRenewalPreview() {
        const base = Math.max(parseFloat(baseInput.value || '0'), 0);
        const tax = base * 0.18;
        const txn = base * 0.025;
        const total = Math.ceil(base + tax + txn);
        const durationMonths = parseInt(durationSelect.value || '12', 10);
        const paymentCount = frequencySelect.value === 'monthly' ? durationMonths : Math.max(1, Math.ceil(durationMonths / 12));
        const amountPerPayment = paymentCount > 0 ? total / paymentCount : total;
        taxPreview.value = formatMoney(tax);
        txnPreview.value = formatMoney(txn);
        totalPreview.value = formatMoney(total);
        paymentCountPreview.value = String(paymentCount);
        paymentAmountPreview.value = formatMoney(amountPerPayment);
    }

    function updateExpiryPreview() {
        const registrationDate = registrationInput.value;
        const durationMonths = parseInt(durationSelect.value || '12', 10);
        if (!registrationDate) { expiryPreview.value = ''; return; }
        const date = new Date(registrationDate + 'T00:00:00');
        date.setMonth(date.getMonth() + durationMonths);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        expiryPreview.value = year + '-' + month + '-' + day;
    }

    baseInput.addEventListener('input', updateRenewalPreview);
    baseInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); updateRenewalPreview(); } });
    frequencySelect.addEventListener('change', updateRenewalPreview);
    registrationInput.addEventListener('input', updateExpiryPreview);
    durationSelect.addEventListener('change', () => { updateExpiryPreview(); updateRenewalPreview(); });

    updateExpiryPreview();
    updateRenewalPreview();
})();
</script>
@endsection
