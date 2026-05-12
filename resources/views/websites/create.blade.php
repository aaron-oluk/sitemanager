@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('websites.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Add Website</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('websites.store') }}" class="space-y-5">
        @csrf

        {{-- Project details --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Project Details</h2>
            </div>
            <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Website Name</label>
                    <input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach(['active','inactive','maintenance'] as $s)
                            <option value="{{ $s }}" {{ old('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Client Name</label>
                    <input name="client_name" value="{{ old('client_name') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Client Email</label>
                    <input type="email" name="client_email" value="{{ old('client_email') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Host Server</label>
                    <input name="host_server" value="{{ old('host_server') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Deployment Date</label>
                    <input type="date" name="deployment_date" value="{{ old('deployment_date') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Domain --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Domain</h2>
            </div>
            <div class="px-5 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Domain Name</label>
                        <input name="domain" value="{{ old('domain') }}" required placeholder="example.com" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Link Existing Domain <span class="normal-case font-normal">(optional)</span></label>
                        <select name="domain_id" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— Create new —</option>
                            @foreach($domains as $id => $domainName)
                                <option value="{{ $id }}" {{ old('domain_id') == $id ? 'selected' : '' }}>{{ $domainName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-2.5">
                    <input type="hidden" name="domain_purchased" value="0">
                    <label class="inline-flex items-center gap-2.5 text-sm font-medium text-gray-700 cursor-pointer select-none">
                        <input id="domain_purchased" type="checkbox" name="domain_purchased" value="1" {{ old('domain_purchased') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        I purchased this domain
                    </label>
                    <input type="hidden" name="amount_includes_domain" value="0">
                    <label class="inline-flex items-center gap-2.5 text-sm font-medium text-gray-700 cursor-pointer select-none">
                        <input type="checkbox" name="amount_includes_domain" value="1" {{ old('amount_includes_domain') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Amount paid already includes domain cost
                    </label>
                </div>

                <div id="domain-cost-input-wrap" class="bg-gray-50 rounded-xl border border-gray-100 px-5 py-4 space-y-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Domain Cost Breakdown</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Base Cost ($)</label>
                            <input id="domain_base_cost" type="number" step="0.01" min="0" name="domain_base_cost" value="{{ old('domain_base_cost', 0) }}" placeholder="17.00" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tax (18%)</label>
                            <input id="domain_tax_preview" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fee (2.5%)</label>
                            <input id="domain_txn_preview" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm text-gray-600" readonly />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Total to Pay</label>
                            <input id="domain_total_preview" type="text" class="w-full rounded-lg border-gray-200 bg-white text-sm font-semibold text-gray-900" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Payment</h2>
            </div>
            <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Amount Paid</label>
                    <input id="amount_paid" type="number" step="0.01" name="amount_paid" value="{{ old('amount_paid') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Currency</label>
                    <select name="currency" class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency }}" {{ old('currency')==$currency?'selected':'' }}>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('websites.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">Save Website</button>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
(function () {
    const checkbox = document.getElementById('domain_purchased');
    const baseInput = document.getElementById('domain_base_cost');
    const inputWrap = document.getElementById('domain-cost-input-wrap');
    const taxPreview = document.getElementById('domain_tax_preview');
    const txnPreview = document.getElementById('domain_txn_preview');
    const totalPreview = document.getElementById('domain_total_preview');

    function formatMoney(value) { return '$' + Number(value || 0).toFixed(2); }

    function updateDomainCostPreview() {
        const purchased = checkbox.checked;
        const base = purchased ? Math.max(parseFloat(baseInput.value || '0'), 0) : 0;
        const tax = base * 0.18;
        const txn = base * 0.025;
        const total = Math.ceil(base + tax + txn);
        baseInput.required = purchased;
        baseInput.disabled = !purchased;
        inputWrap.classList.toggle('opacity-50', !purchased);
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
