@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Website') }}</h2>
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

            <form method="POST" action="{{ route('websites.update', $website) }}" class="bg-white rounded shadow border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website Name</label>
                        <input name="name" value="{{ old('name', $website->name) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain</label>
                        <input name="domain" value="{{ old('domain', $website->domain) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="example.com" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Link to Existing Domain (Optional)</label>
                        <select name="domain_id" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Create New Domain --</option>
                            @foreach($domains as $id => $domainName)
                                <option value="{{ $id }}" {{ old('domain_id', $website->domain_id) == $id ? 'selected' : '' }}>{{ $domainName }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select an existing domain to link, or leave empty to create a new one</p>
                    </div>
                    <div class="md:col-span-2">
                        <input type="hidden" name="domain_purchased" value="0">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input id="domain_purchased" type="checkbox" name="domain_purchased" value="1" {{ old('domain_purchased', $website->domain_purchased) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            I bought this domain
                        </label>
                        <p class="mt-1 text-xs text-gray-500">If unchecked, it means this website is using an existing domain you did not purchase in this entry.</p>
                    </div>
                    <div id="domain-cost-input-wrap">
                        <label class="block text-sm font-medium text-gray-700">Domain Base Cost (Actual Value)</label>
                        <input id="domain_base_cost" type="number" step="0.01" min="0" name="domain_base_cost" value="{{ old('domain_base_cost', $website->domain_base_cost) }}" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="17.00" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax (18%)</label>
                        <input id="domain_tax_preview" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transaction Fee (2.5%)</label>
                        <input id="domain_txn_preview" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Domain Cost</label>
                        <input id="domain_total_preview" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50" readonly />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Host Server</label>
                        <input name="host_server" value="{{ old('host_server', $website->host_server) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deployment Date</label>
                        <input type="date" name="deployment_date" value="{{ old('deployment_date', optional($website->deployment_date)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                        <input type="number" step="0.01" name="amount_paid" value="{{ old('amount_paid', $website->amount_paid) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <select name="currency" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ old('currency', $website->currency)==$currency?'selected':'' }}>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach(['active','inactive','maintenance'] as $status)
                                <option value="{{ $status }}" {{ old('status', $website->status)===$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Name</label>
                        <input name="client_name" value="{{ old('client_name', $website->client_name) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Email</label>
                        <input type="email" name="client_email" value="{{ old('client_email', $website->client_email) }}" required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="4" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">{{ old('description', $website->description) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('websites.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const checkbox = document.getElementById('domain_purchased');
            const baseInput = document.getElementById('domain_base_cost');
            const inputWrap = document.getElementById('domain-cost-input-wrap');
            const taxPreview = document.getElementById('domain_tax_preview');
            const txnPreview = document.getElementById('domain_txn_preview');
            const totalPreview = document.getElementById('domain_total_preview');

            function formatMoney(value) {
                return '$' + Number(value || 0).toFixed(2);
            }

            function updateDomainCostPreview() {
                const purchased = checkbox.checked;
                const base = purchased ? Math.max(parseFloat(baseInput.value || '0'), 0) : 0;
                const tax = base * 0.18;
                const txn = base * 0.025;
                const total = Math.ceil(base + tax + txn);

                baseInput.required = purchased;
                baseInput.disabled = !purchased;
                inputWrap.classList.toggle('opacity-60', !purchased);

                taxPreview.value = formatMoney(tax);
                txnPreview.value = formatMoney(txn);
                totalPreview.value = formatMoney(total);
            }

            checkbox.addEventListener('change', updateDomainCostPreview);
            baseInput.addEventListener('input', updateDomainCostPreview);
            updateDomainCostPreview();
        })();
    </script>
@endsection


