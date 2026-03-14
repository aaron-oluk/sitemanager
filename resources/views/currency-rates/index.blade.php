@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Currency Rates</h2>
                <form method="POST" action="{{ route('currency-rates.refresh') }}">
                    @csrf
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
                        Refresh Rates
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-3 rounded bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-3 rounded bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
            @endif

            @if($needsUpdate)
                <div class="p-3 rounded bg-yellow-50 text-yellow-700 border border-yellow-200 text-sm">
                    Exchange rates are stale or missing. Click <strong>Refresh Rates</strong> to update them.
                </div>
            @endif

            <div class="bg-white rounded shadow border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Current Rates (base: USD)</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate (per 1 USD)</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($currentRates as $currency => $rate)
                            @php
                                $info = app(\App\Services\CurrencyService::class)->getRateInfo($currency);
                                $isStale = $info ? $info['is_stale'] : true;
                                $lastUpdated = $info ? $info['last_updated'] : null;
                                $source = $info['source'] ?? ($currency === 'USD' ? 'system' : 'fallback');
                            @endphp
                            <tr>
                                <td class="px-5 py-3 text-sm font-semibold text-gray-900">{{ $currency }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700 font-mono">
                                    @if($currency === 'USD')
                                        1.000000
                                    @else
                                        {{ number_format(1 / $rate, 6) }}
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-500">
                                    {{ $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->format('M d, Y H:i') : '—' }}
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-500 capitalize">{{ $source }}</td>
                                <td class="px-5 py-3 text-sm">
                                    @if($currency === 'USD')
                                        <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Base</span>
                                    @elseif($isStale)
                                        <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Stale</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Current</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-400 text-center">Rates are used to calculate USD equivalents on payments. Fallback rates apply when no database rate exists.</p>
        </div>
    </div>
@endsection
