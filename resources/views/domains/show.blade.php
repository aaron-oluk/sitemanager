@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $domain->domain_name }}</h2>
            <a href="{{ route('domains.edit', $domain) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">Edit</a>
        </div>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow border border-gray-100 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm text-gray-500">Domain Name</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $domain->domain_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Registrar</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $domain->registrar }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Registration Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($domain->registration_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">
                            @if($domain->isExpiringSoon())
                                <span class="text-orange-600 font-medium">
                                    {{ optional($domain->expiry_date)->format('M d, Y') }} 
                                    ({{ $domain->days_until_expiry }} days)
                                </span>
                            @else
                                {{ optional($domain->expiry_date)->format('M d, Y') }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Annual Cost</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $domain->formatted_annual_cost }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1"><span class="px-2 py-1 rounded-full text-xs {{ $domain->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($domain->status) }}</span></dd>
                    </div>
                </dl>

                <div class="mt-6 p-4 bg-purple-50 rounded border border-purple-200">
                    <h3 class="text-sm font-medium text-purple-900 mb-4">Domain Renewal Calculator</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="renewal_base_cost" class="block text-sm font-medium text-gray-700">Renewal Base Cost</label>
                            <input id="renewal_base_cost" type="number" step="0.01" min="0" value="{{ old('renewal_base_cost', $domain->annual_cost) }}" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-purple-500" />
                            <button id="calculate-renewal" type="button" class="mt-3 px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium">Calculate Renewal</button>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Renewal Tax (18%)</dt>
                                <dd id="renewal_tax_preview" class="mt-1 text-gray-900 font-medium">{{ $domain->formatted_renewal_tax_amount }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Renewal Transaction Fee (2.5%)</dt>
                                <dd id="renewal_txn_preview" class="mt-1 text-gray-900 font-medium">{{ $domain->formatted_renewal_transaction_fee }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Renewal Total Cost</dt>
                                <dd id="renewal_total_preview" class="mt-1 text-gray-900 font-semibold">{{ $domain->formatted_renewal_total_cost }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                @if($domain->website)
                <div class="mt-6 p-4 bg-blue-50 rounded border border-blue-200">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Linked Website</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-900 font-medium">{{ $domain->website->name }}</p>
                            <p class="text-blue-700 text-sm">{{ $domain->website->client_name }}</p>
                        </div>
                        <a href="{{ route('websites.show', $domain->website) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Website →
                        </a>
                    </div>
                </div>
                @endif

                @if($domain->websites->count() > 0)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">All Linked Websites</h3>
                    <div class="space-y-3">
                        @foreach($domain->websites as $website)
                        <div class="flex items-center justify-between p-3 rounded bg-gray-50">
                            <div>
                                <p class="font-medium text-gray-900">{{ $website->name }}</p>
                                <p class="text-sm text-gray-600">{{ $website->client_name }}</p>
                            </div>
                            <a href="{{ route('websites.show', $website) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View →
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($domain->notes)
                    <div class="mt-6">
                        <dt class="text-sm text-gray-500">Notes</dt>
                        <dd class="mt-1 text-gray-900">{{ $domain->notes }}</dd>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        (function () {
            const baseInput = document.getElementById('renewal_base_cost');
            const calculateButton = document.getElementById('calculate-renewal');
            const taxPreview = document.getElementById('renewal_tax_preview');
            const txnPreview = document.getElementById('renewal_txn_preview');
            const totalPreview = document.getElementById('renewal_total_preview');

            if (!baseInput || !calculateButton || !taxPreview || !txnPreview || !totalPreview) {
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

                taxPreview.textContent = formatMoney(tax);
                txnPreview.textContent = formatMoney(txn);
                totalPreview.textContent = formatMoney(total);
            }

            calculateButton.addEventListener('click', updateRenewalPreview);
            baseInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    updateRenewalPreview();
                }
            });

            updateRenewalPreview();
        })();
    </script>
@endsection


