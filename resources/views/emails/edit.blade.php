@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Email Account') }}</h2>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ms-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('emails.update', $email) }}" class="bg-white rounded shadow border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email_address" value="{{ old('email_address', $email->email_address) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain</label>
                        <select name="domain_id" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- No domain association --</option>
                            @foreach ($domains as $id => $domainName)
                                <option value="{{ $id }}" {{ old('domain_id', $email->domain_id) == $id ? 'selected' : '' }}>{{ $domainName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provider</label>
                        <input name="provider" value="{{ old('provider', $email->provider) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hosting Plan</label>
                        <select id="hosting_plan" name="hosting_plan" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach (\App\Models\Email::getHostingPlanOptions() as $value => $label)
                                <option value="{{ $value }}" data-months="{{ ['monthly' => 1, 'quarterly' => 3, 'biannual' => 6, 'annual' => 12, 'biennial' => 24, 'triennial' => 36][$value] }}" {{ old('hosting_plan', $email->hosting_plan) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label id="cost_label" class="block text-sm font-medium text-gray-700">Monthly Cost</label>
                        <input type="number" step="0.01" name="monthly_cost" value="{{ old('monthly_cost', $email->monthly_cost) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Associated Website</label>
                        <select name="website_id" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- No website association --</option>
                            @foreach ($websites as $id => $websiteName)
                                <option value="{{ $id }}" {{ old('website_id', $email->website_id) == $id ? 'selected' : '' }}>{{ $websiteName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date (Auto)</label>
                        <input id="email_start_date_edit" type="date" value="{{ old('start_date', optional($email->start_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Renewal Date (Auto)</label>
                        <input id="email_renewal_date_edit" type="date" value="{{ old('renewal_date', optional($email->renewal_date)->format('Y-m-d')) }}" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'pending' => 'Pending', 'cancelled' => 'Cancelled'] as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $email->status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 rounded border border-blue-200 space-y-4">
                    <h3 class="text-sm font-medium text-blue-900">Email Cost Calculator</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email_monthly_cost_edit" class="block text-sm font-medium text-gray-700">Monthly Cost</label>
                            <input id="email_monthly_cost_edit" type="number" step="0.01" min="0" value="{{ old('monthly_cost', $email->monthly_cost) }}" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                            <button id="calculate-email-cost-edit" type="button" class="mt-3 px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Calculate Cost</button>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Billing Frequency</label>
                                <select id="email_billing_frequency_edit" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="annual">Annual</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Plan Duration (Months)</label>
                                <input id="email_plan_months_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Plan Subtotal</label>
                                <input id="email_subtotal_cost_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tax (18%)</label>
                                <input id="email_tax_cost_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Transaction Fee (2.5%)</label>
                                <input id="email_fee_cost_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Cost</label>
                                <input id="email_total_cost_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Number of Payments</label>
                                <input id="email_payment_count_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount Per Payment</label>
                                <input id="email_payment_amount_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">{{ old('notes', $email->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('emails.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Update</button>
                </div>
            </form>
        </div>
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
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function updateEmailCostPreview() {
            const planSelect = document.getElementById('hosting_plan');
            const costInput = document.getElementById('email_monthly_cost_edit');
            const frequencySelect = document.getElementById('email_billing_frequency_edit');
            const monthsField = document.getElementById('email_plan_months_edit');
            const subtotalField = document.getElementById('email_subtotal_cost_edit');
            const taxField = document.getElementById('email_tax_cost_edit');
            const feeField = document.getElementById('email_fee_cost_edit');
            const totalField = document.getElementById('email_total_cost_edit');
            const paymentCountField = document.getElementById('email_payment_count_edit');
            const paymentAmountField = document.getElementById('email_payment_amount_edit');
            const startDateField = document.getElementById('email_start_date_edit');
            const renewalDateField = document.getElementById('email_renewal_date_edit');

            if (!planSelect || !costInput || !frequencySelect || !monthsField || !subtotalField || !taxField || !feeField || !totalField || !paymentCountField || !paymentAmountField || !startDateField || !renewalDateField) {
                return;
            }

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
            subtotalField.value = '$' + Number(subtotal || 0).toFixed(2);
            taxField.value = '$' + Number(tax || 0).toFixed(2);
            feeField.value = '$' + Number(fee || 0).toFixed(2);
            totalField.value = '$' + Number(total || 0).toFixed(2);
            paymentCountField.value = String(paymentCount);
            paymentAmountField.value = '$' + Number(amountPerPayment || 0).toFixed(2);
            renewalDateField.value = formatDate(renewalDate);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hostingPlan = document.getElementById('hosting_plan');
            const costButton = document.getElementById('calculate-email-cost-edit');
            const costInput = document.getElementById('email_monthly_cost_edit');
            const frequencySelect = document.getElementById('email_billing_frequency_edit');

            updateCostLabel(hostingPlan);
            updateEmailCostPreview();

            if (costButton) {
                costButton.addEventListener('click', updateEmailCostPreview);
            }

            if (costInput) {
                costInput.addEventListener('input', updateEmailCostPreview);
                costInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        updateEmailCostPreview();
                    }
                });
            }

            if (hostingPlan) {
                hostingPlan.addEventListener('change', function () {
                    updateCostLabel(hostingPlan);
                    updateEmailCostPreview();
                });
            }

            if (frequencySelect) {
                frequencySelect.addEventListener('change', updateEmailCostPreview);
            }
        });
    </script>
@endsection