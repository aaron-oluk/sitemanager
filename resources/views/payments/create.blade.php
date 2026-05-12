@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-2xl">

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

        {{-- Website selector --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Select Website</h2>
            </div>
            <div class="px-5 py-4">
                <select id="website-select" name="website_id"
                    class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— Choose a website —</option>
                    @foreach(json_decode(json_encode($websiteData), true) as $w)
                        <option value="{{ $w['id'] }}" {{ old('website_id') == $w['id'] ? 'selected' : '' }}>
                            {{ $w['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Item checkboxes --}}
        <div id="items-section" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">What is being paid for?</h2>
                <p class="text-xs text-gray-400 mt-0.5">Select one or more items</p>
            </div>
            <div id="items-list" class="px-5 py-4 space-y-3"></div>

            {{-- Amount Due total --}}
            <div id="due-total-wrap" class="hidden px-5 pb-5">
                <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-5 py-3.5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Total Amount Due</p>
                        <p class="text-xs text-indigo-400 mt-0.5">Sum of selected items (USD)</p>
                    </div>
                    <p class="text-2xl font-bold text-indigo-900" id="due-total-display">$0.00</p>
                </div>
            </div>
        </div>

        {{-- Payment details --}}
        <div id="details-section" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Payment Details</h2>
            </div>
            <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Amount Paid</label>
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

        <div id="submit-section" class="hidden flex items-center justify-end gap-3">
            <a href="{{ route('payments.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">Record Payment</button>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
(function () {
    const DATA = @json($websiteData);

    const websiteSelect  = document.getElementById('website-select');
    const itemsSection   = document.getElementById('items-section');
    const itemsList      = document.getElementById('items-list');
    const dueTotalWrap   = document.getElementById('due-total-wrap');
    const dueTotalDisplay = document.getElementById('due-total-display');
    const detailsSection = document.getElementById('details-section');
    const submitSection  = document.getElementById('submit-section');
    const amountInput    = document.getElementById('amount-input');
    const currencySelect = document.getElementById('currency-select');
    const statusSummary  = document.getElementById('status-summary');
    const statusLabelText = document.getElementById('status-label-text');
    const statusDescText  = document.getElementById('status-desc-text');
    const statusIcon      = document.getElementById('status-icon');

    let totalAmountDue = 0;
    let amountAutoFilled = false;

    function fmt(amount, currency) {
        const symbols = { USD: '$', UGX: 'USh ', EUR: '€', GBP: '£', KES: 'KSh ', TZS: 'TSh ', NGN: '₦' };
        const sym = symbols[currency] || (currency + ' ');
        const dec = ['UGX', 'TZS'].includes(currency) ? 0 : 2;
        return sym + parseFloat(amount || 0).toFixed(dec);
    }

    function buildItemCard(key, item, checked) {
        const card = document.createElement('label');
        card.className = 'flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors ' +
            (checked ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'selected_items[]';
        checkbox.value = key;
        checkbox.checked = checked;
        checkbox.className = 'mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500';

        checkbox.addEventListener('change', () => {
            card.className = 'flex items-start gap-3 p-4 border-2 rounded-xl cursor-pointer transition-colors ' +
                (checkbox.checked ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300');
            recalcTotal();
        });

        const body = document.createElement('div');
        body.className = 'flex-1 min-w-0';

        // Header row: label + total
        const header = document.createElement('div');
        header.className = 'flex items-center justify-between gap-2';

        const label = document.createElement('span');
        label.className = 'text-sm font-semibold text-gray-900';
        label.textContent = item.label;

        const total = document.createElement('span');
        total.className = 'text-sm font-bold text-gray-900 shrink-0';
        total.textContent = fmt(item.total, item.currency);

        header.append(label, total);

        // Breakdown rows
        const breakdown = document.createElement('div');
        breakdown.className = 'mt-2 space-y-1 border-t border-gray-100 pt-2';

        item.breakdown.forEach(row => {
            const line = document.createElement('div');
            line.className = 'flex items-center justify-between';
            line.innerHTML =
                '<span class="text-xs text-gray-400">' + row.label + '</span>' +
                '<span class="text-xs text-gray-500">' + fmt(row.amount, item.currency) + '</span>';
            breakdown.appendChild(line);
        });

        // Total line in breakdown
        const totalLine = document.createElement('div');
        totalLine.className = 'flex items-center justify-between pt-1 border-t border-gray-200 mt-1';
        totalLine.innerHTML =
            '<span class="text-xs font-semibold text-gray-600">Amount due</span>' +
            '<span class="text-xs font-bold text-gray-800">' + fmt(item.total, item.currency) + '</span>';
        breakdown.appendChild(totalLine);

        body.append(header, breakdown);
        card.append(checkbox, body);
        return card;
    }

    function recalcTotal() {
        const checked = itemsList.querySelectorAll('input[type=checkbox]:checked');
        let total = 0;

        checked.forEach(cb => {
            // find total from DATA
            const website = DATA.find(w => w.id == websiteSelect.value);
            if (!website) return;
            const key = cb.value;
            if (key === 'hosting') total += website.items.hosting.total;
            else if (key === 'domain' && website.items.domain) total += website.items.domain.total;
            else if (key.startsWith('email:')) {
                const id = parseInt(key.replace('email:', ''));
                const em = (website.items.emails || []).find(e => e.id === id);
                if (em) total += em.total;
            }
        });

        totalAmountDue = total;
        dueTotalDisplay.textContent = '$' + total.toFixed(2);
        dueTotalWrap.classList.toggle('hidden', checked.length === 0);
        detailsSection.classList.toggle('hidden', checked.length === 0);
        submitSection.classList.toggle('hidden', checked.length === 0);

        // Auto-fill amount if not manually edited
        if (!amountAutoFilled && total > 0) {
            amountInput.value = total.toFixed(2);
        } else if (amountAutoFilled) {
            amountInput.value = total.toFixed(2);
            amountAutoFilled = true;
        }

        updateStatus();
    }

    function loadWebsite(id) {
        const website = DATA.find(w => w.id == id);
        itemsList.innerHTML = '';

        if (!website) {
            itemsSection.classList.add('hidden');
            detailsSection.classList.add('hidden');
            submitSection.classList.add('hidden');
            return;
        }

        // Set currency to website's currency
        for (let i = 0; i < currencySelect.options.length; i++) {
            if (currencySelect.options[i].value === website.currency) {
                currencySelect.selectedIndex = i;
                break;
            }
        }

        // Hosting item (always present)
        itemsList.appendChild(buildItemCard('hosting', website.items.hosting, true));

        // Domain item
        if (website.items.domain) {
            itemsList.appendChild(buildItemCard('domain', website.items.domain, false));
        }

        // Email items
        (website.items.emails || []).forEach(email => {
            itemsList.appendChild(buildItemCard('email:' + email.id, email, false));
        });

        itemsSection.classList.remove('hidden');
        amountAutoFilled = false;
        recalcTotal();
    }

    function updateStatus() {
        const paid = parseFloat(amountInput.value) || 0;
        const due  = totalAmountDue;

        if (paid <= 0 || due <= 0) {
            statusSummary.classList.add('hidden');
            return;
        }

        const complete = paid >= due;
        const balance  = due - paid;

        statusSummary.classList.remove('hidden');

        if (complete) {
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
            statusDescText.textContent = '$' + balance.toFixed(2) + ' still outstanding. Status will be set to Pending.';
            statusIcon.className = 'h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0';
            statusIcon.innerHTML = '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }
    }

    websiteSelect.addEventListener('change', () => loadWebsite(websiteSelect.value));

    amountInput.addEventListener('input', () => {
        amountAutoFilled = false;
        updateStatus();
    });

    // Restore on validation failure
    if (websiteSelect.value) {
        loadWebsite(websiteSelect.value);
    }
})();
</script>
@endsection
