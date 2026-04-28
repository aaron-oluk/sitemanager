@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Domain') }}</h2>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ms-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('domains.update', $domain) }}" class="bg-white rounded shadow border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain Name</label>
                        <input name="domain_name" value="{{ old('domain_name', $domain->domain_name) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registrar</label>
                        <input name="registrar" value="{{ old('registrar', $domain->registrar) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registration Date</label>
                        <input type="date" name="registration_date" value="{{ old('registration_date', optional($domain->registration_date)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subscription Duration</label>
                        <select id="subscription_duration_edit" name="subscription_duration_months" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500">
                            @foreach($durationOptions as $months => $label)
                                <option value="{{ $months }}" {{ old('subscription_duration_months', $selectedDurationMonths) == $months ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Calculated Expiry Date</label>
                        <input id="calculated_expiry_edit" type="date" value="{{ optional($domain->expiry_date)->format('Y-m-d') }}" readonly class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Annual Cost</label>
                        <input type="number" step="0.01" name="annual_cost" value="{{ old('annual_cost', $domain->annual_cost) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500">
                            @foreach(['active','expired','pending'] as $status)
                                <option value="{{ $status }}" {{ old('status', $domain->status)===$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="p-4 bg-purple-50 rounded border border-purple-200 space-y-4">
                    <h3 class="text-sm font-medium text-purple-900">Domain Renewal Calculator</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="renewal_base_cost_edit" class="block text-sm font-medium text-gray-700">Renewal Base Cost</label>
                            <input id="renewal_base_cost_edit" type="number" step="0.01" min="0" value="{{ old('annual_cost', $domain->annual_cost) }}" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500" />
                            <button id="calculate-renewal-edit" type="button" class="mt-3 px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">Calculate Renewal</button>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Renewal Tax (18%)</label>
                                <input id="renewal_tax_preview_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Renewal Transaction Fee (2.5%)</label>
                                <input id="renewal_txn_preview_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Renewal Total Cost</label>
                                <input id="renewal_total_preview_edit" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500">{{ old('notes', $domain->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('domains.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    <button class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const registrationInput = document.querySelector('input[name="registration_date"]');
            const durationSelect = document.getElementById('subscription_duration_edit');
            const expiryPreview = document.getElementById('calculated_expiry_edit');
            const baseInput = document.getElementById('renewal_base_cost_edit');
            const calculateButton = document.getElementById('calculate-renewal-edit');
            const taxPreview = document.getElementById('renewal_tax_preview_edit');
            const txnPreview = document.getElementById('renewal_txn_preview_edit');
            const totalPreview = document.getElementById('renewal_total_preview_edit');

            if (!registrationInput || !durationSelect || !expiryPreview || !baseInput || !calculateButton || !taxPreview || !txnPreview || !totalPreview) {
                return;
            }

            function formatMoney(value) {
                return '$' + Number(value || 0).toFixed(2);
            }

            function updateRenewalPreview() {
                const base = Math.max(parseFloat(baseInput.value || '0'), 0);
                const tax = base * 0.18;
                const txn = base * 0.025;
                const total = Math.ceil(base + tax + txn);

                taxPreview.value = formatMoney(tax);
                txnPreview.value = formatMoney(txn);
                totalPreview.value = formatMoney(total);
            }

            function updateExpiryPreview() {
                const registrationDate = registrationInput.value;
                const durationMonths = parseInt(durationSelect.value || '12', 10);

                if (!registrationDate) {
                    expiryPreview.value = '';
                    return;
                }

                const date = new Date(registrationDate + 'T00:00:00');
                date.setMonth(date.getMonth() + durationMonths);

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                expiryPreview.value = year + '-' + month + '-' + day;
            }

            calculateButton.addEventListener('click', updateRenewalPreview);
            registrationInput.addEventListener('input', updateExpiryPreview);
            durationSelect.addEventListener('change', updateExpiryPreview);
            baseInput.addEventListener('input', updateRenewalPreview);
            baseInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    updateRenewalPreview();
                }
            });

            updateExpiryPreview();
            updateRenewalPreview();
        })();
    </script>
@endsection


