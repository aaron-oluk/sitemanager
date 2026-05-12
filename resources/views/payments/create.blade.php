@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Record Payment</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('payments.store') }}" class="space-y-5" id="payment-form">
        @csrf

        {{-- Step 1: Payment type --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">What is this payment for?</h2>
            </div>
            <div class="px-5 py-4">
                <div class="flex gap-2" id="type-buttons">
                    <button type="button" data-type="website"
                        class="type-btn px-4 py-2 rounded-lg border text-sm font-medium transition-colors
                            {{ old('payment_type') === 'website' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 text-gray-600 hover:border-gray-400 hover:text-gray-800' }}">
                        Website
                    </button>
                    <button type="button" data-type="domain"
                        class="type-btn px-4 py-2 rounded-lg border text-sm font-medium transition-colors
                            {{ old('payment_type') === 'domain' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 text-gray-600 hover:border-gray-400 hover:text-gray-800' }}">
                        Domain
                    </button>
                    <button type="button" data-type="email"
                        class="type-btn px-4 py-2 rounded-lg border text-sm font-medium transition-colors
                            {{ old('payment_type') === 'email' ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 text-gray-600 hover:border-gray-400 hover:text-gray-800' }}">
                        Email Account
                    </button>
                </div>
                <input type="hidden" name="payment_type" id="payment_type" value="{{ old('payment_type', '') }}" />
            </div>
        </div>

        {{-- Step 2: Choose item + amount due breakdown --}}
        <div id="item-section" class="{{ old('payment_type') ? '' : 'hidden' }} bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900" id="item-section-title">Select item</h2>
            </div>
            <div class="px-5 py-5 space-y-4">

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1" id="item-label">Item</label>
                    <select id="item-select" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Select —</option>
                    </select>
                    <input type="hidden" name="website_id" id="website_id" value="{{ old('website_id') }}" />
                    <input type="hidden" name="domain_id"  id="domain_id"  value="{{ old('domain_id') }}" />
                    <input type="hidden" name="email_id"   id="email_id"   value="{{ old('email_id') }}" />
                </div>

                {{-- Amount due with breakdown --}}
                <div id="due-panel" class="hidden">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount Due</p>
                        </div>
                        {{-- Breakdown rows --}}
                        <div id="breakdown-rows" class="divide-y divide-gray-100"></div>
                        {{-- Amount Due row --}}
                        <div class="px-5 py-3.5 flex items-center justify-between bg-indigo-50 border-t border-indigo-100">
                            <div>
                                <span class="text-sm font-semibold text-indigo-900">Amount Due</span>
                                <p class="text-xs text-indigo-500 mt-0.5">This is what should be paid</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-bold text-indigo-900" id="due-amount-display">—</span>
                                <span class="text-xs text-indigo-400 ml-1" id="due-currency-display"></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="amount_due" id="amount_due" value="{{ old('amount_due') }}" />
                </div>
            </div>
        </div>

        {{-- Step 3: Payment details --}}
        <div id="details-section" class="{{ old('payment_type') ? '' : 'hidden' }} bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Payment Details</h2>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Amount Being Paid</label>
                    <input type="number" name="amount" id="amount-input" step="0.01" min="0"
                        value="{{ old('amount') }}"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="0.00" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Currency</label>
                    <select name="currency" id="currency-select" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($currencies as $c)
                            <option value="{{ $c }}" {{ old('currency', 'USD') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Payment Method</label>
                    <input type="text" name="payment_method" value="{{ old('payment_method') }}"
                        placeholder="e.g. Bank transfer, Mobile money"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Transaction ID <span class="normal-case font-normal">(optional)</span></label>
                    <input type="text" name="transaction_id" value="{{ old('transaction_id') }}"
                        class="w-full rounded-lg border-gray-300 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes <span class="normal-case font-normal">(optional)</span></label>
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Status summary --}}
            <div class="px-5 pb-5">
                <div id="status-summary" class="hidden rounded-xl border px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" id="status-label-text"></p>
                        <p class="text-sm" id="status-desc-text"></p>
                    </div>
                    <div id="status-icon" class="h-10 w-10 rounded-full flex items-center justify-center shrink-0"></div>
                </div>
            </div>
        </div>

        <div id="submit-section" class="{{ old('payment_type') ? '' : 'hidden' }} flex items-center justify-end gap-3">
            <a href="{{ route('payments.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">Record Payment</button>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
(function () {
    const DATA = {
        website: @json($websiteData),
        domain:  @json($domainData),
        email:   @json($emailData),
    };

    const LABELS = {
        website: { section: 'Select Website',       item: 'Website' },
        domain:  { section: 'Select Domain',         item: 'Domain' },
        email:   { section: 'Select Email Account',  item: 'Email Account' },
    };

    let currentType      = document.getElementById('payment_type').value || null;
    let currentAmountDue = parseFloat(document.getElementById('amount_due').value) || 0;
    let currentCurrency  = 'USD';

    const typeButtons      = document.querySelectorAll('.type-btn');
    const paymentTypeInput = document.getElementById('payment_type');
    const itemSection      = document.getElementById('item-section');
    const itemSectionTitle = document.getElementById('item-section-title');
    const itemLabel        = document.getElementById('item-label');
    const itemSelect       = document.getElementById('item-select');
    const duePanel         = document.getElementById('due-panel');
    const breakdownRows    = document.getElementById('breakdown-rows');
    const dueAmountDisplay = document.getElementById('due-amount-display');
    const dueCurrencyDisplay = document.getElementById('due-currency-display');
    const amountDueInput   = document.getElementById('amount_due');
    const amountInput      = document.getElementById('amount-input');
    const currencySelect   = document.getElementById('currency-select');
    const detailsSection   = document.getElementById('details-section');
    const submitSection    = document.getElementById('submit-section');
    const statusSummary    = document.getElementById('status-summary');
    const statusLabelText  = document.getElementById('status-label-text');
    const statusDescText   = document.getElementById('status-desc-text');
    const statusIcon       = document.getElementById('status-icon');

    function fmt(amount, currency) {
        const symbols = {USD:'$', UGX:'USh ', EUR:'€', GBP:'£', KES:'KSh ', TZS:'TSh ', NGN:'₦'};
        const sym = symbols[currency] || (currency + ' ');
        const dec = ['UGX','TZS'].includes(currency) ? 0 : 2;
        return sym + parseFloat(amount || 0).toFixed(dec);
    }

    function renderBreakdown(breakdown, total, currency) {
        breakdownRows.innerHTML = '';
        if (!breakdown || breakdown.length === 0) return;
        breakdown.forEach(row => {
            const div = document.createElement('div');
            div.className = 'px-5 py-2.5 flex items-center justify-between';
            div.innerHTML =
                '<span class="text-sm text-gray-500">' + row.label + '</span>' +
                '<span class="text-sm font-medium text-gray-700">' + fmt(row.amount, currency) + '</span>';
            breakdownRows.appendChild(div);
        });
    }

    function selectType(type) {
        currentType = type;
        paymentTypeInput.value = type;

        typeButtons.forEach(btn => {
            const active = btn.dataset.type === type;
            btn.className = 'type-btn px-4 py-2 rounded-lg border text-sm font-medium transition-colors ' +
                (active
                    ? 'bg-indigo-600 border-indigo-600 text-white'
                    : 'border-gray-300 text-gray-600 hover:border-gray-400 hover:text-gray-800');
        });

        const items = DATA[type] || [];
        itemSelect.innerHTML = '<option value="">— Select —</option>';
        items.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.label;
            opt.dataset.amountDue = item.amount_due;
            opt.dataset.currency  = item.currency;
            opt.dataset.breakdown = JSON.stringify(item.breakdown || []);
            itemSelect.appendChild(opt);
        });

        itemSectionTitle.textContent = LABELS[type].section;
        itemLabel.textContent = LABELS[type].item;

        itemSection.classList.remove('hidden');
        detailsSection.classList.remove('hidden');
        submitSection.classList.remove('hidden');
        duePanel.classList.add('hidden');
        breakdownRows.innerHTML = '';

        document.getElementById('website_id').value = '';
        document.getElementById('domain_id').value  = '';
        document.getElementById('email_id').value   = '';

        updateStatusSummary();
    }

    function selectItem(select) {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            duePanel.classList.add('hidden');
            amountDueInput.value = '';
            currentAmountDue = 0;
            updateStatusSummary();
            return;
        }

        currentAmountDue = parseFloat(opt.dataset.amountDue) || 0;
        currentCurrency  = opt.dataset.currency || 'USD';
        const breakdown  = JSON.parse(opt.dataset.breakdown || '[]');

        document.getElementById('website_id').value = currentType === 'website' ? opt.value : '';
        document.getElementById('domain_id').value  = currentType === 'domain'  ? opt.value : '';
        document.getElementById('email_id').value   = currentType === 'email'   ? opt.value : '';

        amountDueInput.value = currentAmountDue;

        for (let i = 0; i < currencySelect.options.length; i++) {
            if (currencySelect.options[i].value === currentCurrency) {
                currencySelect.selectedIndex = i;
                break;
            }
        }

        renderBreakdown(breakdown, currentAmountDue, currentCurrency);
        dueAmountDisplay.textContent = fmt(currentAmountDue, currentCurrency);
        dueCurrencyDisplay.textContent = currentCurrency !== 'USD' ? currentCurrency : '';
        duePanel.classList.remove('hidden');

        // Pre-fill amount being paid with amount due
        if (!amountInput.value || amountInput.dataset.autofilled === '1') {
            amountInput.value = currentAmountDue > 0 ? currentAmountDue.toFixed(2) : '';
            amountInput.dataset.autofilled = '1';
        }

        updateStatusSummary();
    }

    function updateStatusSummary() {
        const amountPaid = parseFloat(amountInput.value) || 0;
        const amountDue  = currentAmountDue;

        if (!currentType || !itemSelect.value || amountPaid <= 0) {
            statusSummary.classList.add('hidden');
            return;
        }

        const isComplete = amountDue > 0 && amountPaid >= amountDue;
        const balance    = amountDue - amountPaid;

        statusSummary.classList.remove('hidden');

        if (isComplete) {
            statusSummary.className = 'rounded-xl border border-green-200 bg-green-50 px-5 py-4 flex items-center justify-between';
            statusLabelText.className = 'text-xs font-semibold uppercase tracking-wider mb-0.5 text-green-700';
            statusLabelText.textContent = 'Payment Complete';
            statusDescText.className = 'text-sm text-green-600';
            statusDescText.textContent = 'Full amount covered. Status will be set to Completed.';
            statusIcon.className = 'h-10 w-10 rounded-full bg-green-100 flex items-center justify-center shrink-0';
            statusIcon.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else {
            statusSummary.className = 'rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-center justify-between';
            statusLabelText.className = 'text-xs font-semibold uppercase tracking-wider mb-0.5 text-amber-700';
            statusLabelText.textContent = 'Partial Payment';
            statusDescText.className = 'text-sm text-amber-600';
            statusDescText.textContent = amountDue > 0
                ? fmt(balance, currentCurrency) + ' still outstanding. Status will be set to Pending.'
                : 'Status will be set to Pending.';
            statusIcon.className = 'h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0';
            statusIcon.innerHTML = '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }
    }

    typeButtons.forEach(btn => {
        btn.addEventListener('click', () => selectType(btn.dataset.type));
    });

    itemSelect.addEventListener('change', () => selectItem(itemSelect));
    amountInput.addEventListener('input', () => {
        amountInput.dataset.autofilled = '0';
        updateStatusSummary();
    });

    if (currentType) {
        selectType(currentType);
        const oldId = document.getElementById(currentType + '_id').value;
        if (oldId) {
            for (let i = 0; i < itemSelect.options.length; i++) {
                if (itemSelect.options[i].value == oldId) {
                    itemSelect.selectedIndex = i;
                    selectItem(itemSelect);
                    break;
                }
            }
        }
    }
})();
</script>
@endsection
