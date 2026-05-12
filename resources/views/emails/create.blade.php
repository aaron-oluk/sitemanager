@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('emails.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Add Email Account</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('emails.store') }}" class="space-y-5">
        @csrf

        {{-- Associated website (drives auto-fill) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Associated Website</h2>
                <p class="text-xs text-gray-400 mt-0.5">Selecting a website auto-fills the domain and provider below.</p>
            </div>
            <div class="px-5 py-4">
                <select id="website_select" name="website_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— No website —</option>
                    @foreach($websites as $id => $websiteName)
                        <option value="{{ $id }}" {{ old('website_id') == $id ? 'selected' : '' }}>{{ $websiteName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Account details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Account Details</h2>
            </div>
            <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                    <input name="email_address" id="email_address_input" value="{{ old('email_address') }}" required placeholder="user@domain.com" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Domain</label>
                    <select id="domain_select" name="domain_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">— Select domain —</option>
                        @foreach($domains as $id => $domainName)
                            <option value="{{ $id }}" {{ old('domain_id') == $id ? 'selected' : '' }}>{{ $domainName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Provider</label>
                    <input id="provider_input" name="provider" value="{{ old('provider') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Hosting Plan</label>
                    <select id="hosting_plan" name="hosting_plan" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(\App\Models\Email::getHostingPlanOptions() as $value => $label)
                            <option value="{{ $value }}" data-months="{{ ['monthly' => 1, 'quarterly' => 3, 'biannual' => 6, 'annual' => 12, 'biennial' => 24, 'triennial' => 36][$value] }}" {{ old('hosting_plan') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label id="cost_label" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Monthly Cost</label>
                    <input type="number" step="0.01" name="monthly_cost" value="{{ old('monthly_cost') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'pending' => 'Pending', 'cancelled' => 'Cancelled'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Renewal Date <span class="normal-case font-normal">(calculated)</span></label>
                    <input id="email_renewal_date_create" type="date" readonly class="w-full rounded-lg border-gray-200 bg-gray-50 text-sm text-gray-600" />
                    <input type="hidden" id="email_start_date_create" value="{{ now()->format('Y-m-d') }}" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Cost calculator --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Cost Breakdown</h2>
                <p class="text-xs text-gray-500 mt-0.5">Calculated from the hosting plan and monthly cost above.</p>
            </div>
            <div class="px-5 py-4">
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Billing Frequency</label>
                    <select id="email_billing_frequency_create" class="w-56 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="monthly" selected>Monthly</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-100 px-5 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Duration</p>
                            <input id="email_plan_months_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Subtotal</p>
                            <input id="email_subtotal_cost_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tax (18%)</p>
                            <input id="email_tax_cost_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Fee (2.5%)</p>
                            <input id="email_fee_cost_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total</p>
                            <input id="email_total_cost_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm font-semibold text-gray-900" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">No. of Payments</p>
                            <input id="email_payment_count_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Per Payment</p>
                            <input id="email_payment_amount_create" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm font-semibold text-gray-900" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('emails.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">Save Account</button>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
function updateCostLabel(select) {
    const months = select.options[select.selectedIndex].dataset.months;
    const label = document.getElementById('cost_label');
    label.textContent = 'Monthly Cost (' + months + (months == 1 ? ' month)' : ' months)');
}

function addMonthsNoOverflow(date, months) {
    const result = new Date(date.getTime());
    const day = result.getDate();
    result.setDate(1);
    result.setMonth(result.getMonth() + months);
    const maxDay = new Date(result.getFullYear(), result.getMonth() + 1, 0).getDate();
    result.setDate(Math.min(day, maxDay));
    return result;
}

function formatDate(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
}

function updateEmailCostPreview() {
    const planSelect = document.getElementById('hosting_plan');
    const costInput = document.querySelector('input[name="monthly_cost"]');
    const frequencySelect = document.getElementById('email_billing_frequency_create');
    const monthsField = document.getElementById('email_plan_months_create');
    const subtotalField = document.getElementById('email_subtotal_cost_create');
    const taxField = document.getElementById('email_tax_cost_create');
    const feeField = document.getElementById('email_fee_cost_create');
    const totalField = document.getElementById('email_total_cost_create');
    const paymentCountField = document.getElementById('email_payment_count_create');
    const paymentAmountField = document.getElementById('email_payment_amount_create');
    const startDateField = document.getElementById('email_start_date_create');
    const renewalDateField = document.getElementById('email_renewal_date_create');

    const selectedOption = planSelect.options[planSelect.selectedIndex];
    const months = parseInt(selectedOption.dataset.months || '1', 10);
    const monthly = Math.max(parseFloat(costInput.value || '0'), 0);
    const subtotal = monthly * months;
    const tax = subtotal * 0.18;
    const fee = subtotal * 0.025;
    const total = Math.ceil(subtotal + tax + fee);
    const paymentCount = frequencySelect.value === 'monthly' ? months : Math.max(1, Math.ceil(months / 12));
    const amountPerPayment = paymentCount > 0 ? total / paymentCount : total;
    const startDate = new Date(startDateField.value + 'T00:00:00');
    const renewalDate = addMonthsNoOverflow(startDate, months);

    monthsField.value = months;
    subtotalField.value = '$' + subtotal.toFixed(2);
    taxField.value = '$' + tax.toFixed(2);
    feeField.value = '$' + fee.toFixed(2);
    totalField.value = '$' + total.toFixed(2);
    paymentCountField.value = String(paymentCount);
    paymentAmountField.value = '$' + amountPerPayment.toFixed(2);
    renewalDateField.value = formatDate(renewalDate);
}

document.addEventListener('DOMContentLoaded', function () {
    const hostingPlan     = document.getElementById('hosting_plan');
    const costInput       = document.querySelector('input[name="monthly_cost"]');
    const frequencySelect = document.getElementById('email_billing_frequency_create');

    updateCostLabel(hostingPlan);
    updateEmailCostPreview();

    costInput.addEventListener('input', updateEmailCostPreview);
    costInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); updateEmailCostPreview(); } });
    hostingPlan.addEventListener('change', function () { updateCostLabel(hostingPlan); updateEmailCostPreview(); });
    frequencySelect.addEventListener('change', updateEmailCostPreview);

    // Website auto-fill
    const WEBSITE_DATA   = @json($websiteData);
    const websiteSelect  = document.getElementById('website_select');
    const domainSelect   = document.getElementById('domain_select');
    const providerInput  = document.getElementById('provider_input');
    const emailInput     = document.getElementById('email_address_input');

    function applyWebsiteAutofill(websiteId) {
        const w = WEBSITE_DATA[websiteId];
        if (!w) return;

        // Auto-fill provider from website's host server
        if (w.host_server && !providerInput.value) {
            providerInput.value = w.host_server;
        }

        // Auto-select domain if matched
        if (w.domain_id) {
            for (let i = 0; i < domainSelect.options.length; i++) {
                if (domainSelect.options[i].value == w.domain_id) {
                    domainSelect.selectedIndex = i;

                    // Suggest the domain in the email address placeholder
                    if (w.domain_name && emailInput && !emailInput.value) {
                        emailInput.placeholder = 'user@' + w.domain_name;
                    }
                    break;
                }
            }
        }
    }

    websiteSelect.addEventListener('change', function () {
        applyWebsiteAutofill(this.value);
    });

    // Apply on load if value already selected (e.g. validation failure)
    if (websiteSelect.value) {
        applyWebsiteAutofill(websiteSelect.value);
    }
});
</script>
@endsection
