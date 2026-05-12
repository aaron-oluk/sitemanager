@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Currency Rates</h1>
            <p class="text-sm text-gray-500 mt-0.5">Exchange rates relative to USD (1 USD = X currency)</p>
        </div>
        <form method="POST" action="{{ route('currency-rates.refresh') }}">
            @csrf
            <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh Rates
            </button>
        </form>
    </div>

    @if($needsUpdate)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-medium text-amber-800">Exchange rates are stale or missing. Click <strong>Refresh Rates</strong> to update them.</p>
    </div>
    @endif

    {{-- Rates table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Currency</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate (per 1 USD)</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Updated</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Source</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($currentRates as $currency => $rate)
                @php
                    $info = app(\App\Services\CurrencyService::class)->getRateInfo($currency);
                    $isStale = $info ? $info['is_stale'] : true;
                    $lastUpdated = $info ? $info['last_updated'] : null;
                    $source = $info['source'] ?? ($currency === 'USD' ? 'system' : 'fallback');
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 text-sm font-semibold text-gray-900">{{ $currency }}</td>
                    <td class="px-5 py-3.5 text-sm font-mono text-gray-700">
                        @if($currency === 'USD')
                            1.000000
                        @else
                            {{ number_format(1 / $rate, 6) }}
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-500">
                        {{ $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->format('M j, Y H:i') : '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-500 capitalize">{{ $source }}</td>
                    <td class="px-5 py-3.5">
                        @if($currency === 'USD')
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-blue-100 text-blue-700">Base</span>
                        @elseif($isStale)
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-amber-100 text-amber-700">Stale</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-green-100 text-green-700">Current</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-400 text-center">Rates are used to calculate USD equivalents on payments. Fallback rates apply when no live rate is available.</p>

</div>
@endsection
