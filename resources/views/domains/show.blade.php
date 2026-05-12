@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-4xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('domains.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $domain->domain_name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $domain->registrar }}</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $domain->status === 'active' ? 'bg-green-100 text-green-700' :
                   ($domain->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                {{ ucfirst($domain->status) }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('domains.edit', $domain) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form action="{{ route('domains.destroy', $domain) }}" method="POST" onsubmit="return confirm('Delete this domain?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-sm font-medium text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @php
        $daysLeft = $domain->expiry_date ? now()->diffInDays($domain->expiry_date, false) : null;
        $expired  = $daysLeft !== null && $daysLeft < 0;
        $urgent   = !$expired && $daysLeft !== null && $daysLeft <= 7;
        $warning  = !$expired && $daysLeft !== null && $daysLeft <= 30;
    @endphp

    {{-- Expiry alert --}}
    @if($urgent)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-medium text-red-800">Domain expires in {{ (int)$daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }} — renew immediately.</p>
    </div>
    @elseif($warning)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-medium text-amber-800">Domain expires in {{ (int)$daysLeft }} days. Plan your renewal.</p>
    </div>
    @elseif($expired)
    <div class="bg-gray-100 border border-gray-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-gray-700">Domain expired {{ abs((int)$daysLeft) }} days ago.</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Registration details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Registration</h2>
                </div>
                <dl class="px-5 py-4 grid grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ optional($domain->registration_date)->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</dt>
                        <dd class="mt-1 text-sm font-medium {{ $urgent ? 'text-red-700' : ($warning ? 'text-amber-700' : 'text-gray-900') }}">
                            {{ optional($domain->expiry_date)->format('M j, Y') }}
                            @if($daysLeft !== null && !$expired)
                                <span class="block text-xs font-normal text-gray-400 mt-0.5">{{ (int)$daysLeft }} days left</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Annual Cost</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $domain->formatted_annual_cost }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registrar</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $domain->registrar }}</dd>
                    </div>
                </dl>
                @if($domain->notes)
                <div class="px-5 pb-4 border-t border-gray-50 pt-3">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</p>
                    <p class="text-sm text-gray-700">{{ $domain->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Renewal calculator --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Renewal Cost Estimator</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="flex items-end gap-3 mb-4">
                        <div class="flex-1">
                            <label for="renewal_base_cost" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Base Cost ($)</label>
                            <input id="renewal_base_cost" type="number" step="0.01" min="0"
                                value="{{ old('renewal_base_cost', $domain->annual_cost) }}"
                                class="w-full rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <button id="calculate-renewal" type="button" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors whitespace-nowrap">
                            Calculate
                        </button>
                    </div>
                    <div class="space-y-2 text-sm bg-gray-50 rounded-lg px-4 py-3">
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (18%)</span>
                            <span id="renewal_tax_preview" class="font-medium">{{ $domain->formatted_renewal_tax_amount }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Transaction fee (2.5%)</span>
                            <span id="renewal_txn_preview" class="font-medium">{{ $domain->formatted_renewal_transaction_fee }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total to pay</span>
                            <span id="renewal_total_preview">{{ $domain->formatted_renewal_total_cost }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Linked websites --}}
            @if($domain->websites->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Linked Websites</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($domain->websites as $website)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $website->name }}</p>
                            <p class="text-xs text-gray-500">{{ $website->client_name }}</p>
                        </div>
                        <a href="{{ route('websites.show', $website) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View →</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar summary --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Summary</h2>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500">Annual cost</p>
                        <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $domain->formatted_annual_cost }}</p>
                    </div>
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-2">Renewal breakdown</p>
                        <div class="space-y-1 text-xs text-gray-600">
                            <div class="flex justify-between"><span>+ Tax</span><span>{{ $domain->formatted_renewal_tax_amount }}</span></div>
                            <div class="flex justify-between"><span>+ Fee</span><span>{{ $domain->formatted_renewal_transaction_fee }}</span></div>
                            <div class="flex justify-between font-semibold text-gray-900 pt-1 border-t border-gray-100"><span>Total</span><span>{{ $domain->formatted_renewal_total_cost }}</span></div>
                        </div>
                    </div>
                    @if($domain->emails->count() > 0)
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Email accounts</p>
                        <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $domain->emails->count() }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
(function () {
    const baseInput = document.getElementById('renewal_base_cost');
    const btn = document.getElementById('calculate-renewal');
    const taxEl = document.getElementById('renewal_tax_preview');
    const txnEl = document.getElementById('renewal_txn_preview');
    const totalEl = document.getElementById('renewal_total_preview');

    function calc() {
        const base = Math.max(parseFloat(baseInput.value || '0'), 0);
        const tax = base * 0.18;
        const txn = base * 0.025;
        const total = Math.ceil(base + tax + txn);
        const fmt = v => '$' + v.toFixed(2);
        taxEl.textContent = fmt(tax);
        txnEl.textContent = fmt(txn);
        totalEl.textContent = '$' + total.toFixed(2);
    }

    btn.addEventListener('click', calc);
    baseInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); calc(); } });
})();
</script>
@endsection
