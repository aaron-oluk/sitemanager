@extends('layouts.app')

@section('content')
<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payments</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $totalPayments }} recorded · all completed</p>
        </div>
        <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Record Payment
        </a>
    </div>

    {{-- Revenue summary --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">USD equivalent, completed payments</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ now()->format('F Y') }}</p>
            <p class="text-2xl font-bold text-gray-900">${{ number_format($monthRevenue, 2) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">This month's revenue</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">For</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">USD equiv.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="text-sm font-medium text-gray-900 mb-1">
                            {{ $payment->website->name ?? $payment->domain->domain_name ?? 'N/A' }}
                        </p>
                        <div class="flex flex-wrap gap-1">
                            @if($payment->lineItems->count())
                                @foreach($payment->lineItems as $item)
                                    <span class="text-xs px-1.5 py-0.5 rounded font-medium
                                        {{ $item->item_type === 'domain' ? 'bg-purple-100 text-purple-700' :
                                           ($item->item_type === 'email'  ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($item->item_type) }}
                                    </span>
                                @endforeach
                            @else
                                {{-- legacy payments without line items --}}
                                <span class="text-xs px-1.5 py-0.5 rounded font-medium
                                    {{ $payment->payment_type === 'domain' ? 'bg-purple-100 text-purple-700' :
                                       ($payment->payment_type === 'email'  ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($payment->payment_type ?? 'website') }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $payment->payment_method }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ optional($payment->payment_date)->format('M j, Y') }}</td>
                    <td class="px-5 py-3.5 text-sm font-semibold text-gray-900 text-right">{{ $payment->formatted_amount }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-500 text-right">
                        @if($payment->currency !== 'USD')
                            {{ $payment->formatted_usd_equivalent }}
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' :
                               ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 text-sm">
                            <a href="{{ route('payments.receipt', $payment) }}" class="text-gray-400 hover:text-gray-600" title="View receipt">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                            <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                            <a href="{{ route('payments.edit', $payment) }}" class="text-gray-500 hover:text-gray-700">Edit</a>
                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Delete this payment?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">
                        No payments recorded yet.
                        <a href="{{ route('payments.create') }}" class="text-indigo-600 hover:underline ml-1">Record one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">{{ $payments->links() }}</div>
    </div>

</div>
@endsection
