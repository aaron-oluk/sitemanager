@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payment</h1>
                @if($payment->receipt_number)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $payment->receipt_number }}</p>
                @endif
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' :
                   ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                {{ ucfirst($payment->status) }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.receipt', $payment) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Receipt
            </a>
            <a href="{{ route('payments.download-receipt', $payment) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
            <a href="{{ route('payments.edit', $payment) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
                Edit
            </a>
        </div>
    </div>

    {{-- Amount summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Amount Received</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $payment->formatted_amount }}</p>
                    @if($payment->currency !== 'USD')
                        <p class="text-sm text-gray-400 mt-1">≈ {{ $payment->formatted_usd_equivalent }}</p>
                    @endif
                </div>
                @if($payment->amount_due)
                <div class="text-right shrink-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Amount Due</p>
                    <p class="text-2xl font-semibold text-gray-700">${{ number_format($payment->amount_due, 2) }}</p>
                    @php $balance = (float)$payment->amount_due - (float)$payment->amount; @endphp
                    @if($balance <= 0)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Paid in full
                        </span>
                    @else
                        <span class="inline-block text-xs font-medium text-amber-600 mt-1">${{ number_format($balance, 2) }} outstanding</span>
                    @endif
                </div>
                @endif
                <div class="h-14 w-14 rounded-xl {{ $payment->status === 'completed' ? 'bg-green-50' : 'bg-amber-50' }} flex items-center justify-center shrink-0">
                    @if($payment->status === 'completed')
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
            </div>
        </div>
        {{-- Line items audit table --}}
        @if($payment->lineItems->count())
        <div class="px-6 pt-5 pb-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Items &amp; Cost Breakdown</p>
            <div class="rounded-xl border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Cost</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tax (18%)</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Fee (2.5%)</th>
                            <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($payment->lineItems as $item)
                        @php
                            $sym = ['USD'=>'$','UGX'=>'USh ','EUR'=>'€','GBP'=>'£','KES'=>'KSh ','TZS'=>'TSh ','NGN'=>'₦'][$item->currency] ?? ($item->currency . ' ');
                            $dec = in_array($item->currency, ['UGX','TZS']) ? 0 : 2;
                        @endphp
                        <tr class="bg-white">
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900">{{ $item->label }}</span>
                                <span class="ml-2 text-xs px-1.5 py-0.5 rounded font-medium
                                    {{ $item->item_type === 'domain' ? 'bg-purple-100 text-purple-600' :
                                       ($item->item_type === 'email'  ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }}">
                                    {{ ucfirst($item->item_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $sym }}{{ number_format($item->unit_cost, $dec) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $sym }}{{ number_format($item->tax_amount, $dec) }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">{{ $sym }}{{ number_format($item->transaction_fee, $dec) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $sym }}{{ number_format($item->total_amount, $dec) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    @if($payment->lineItems->count() > 1)
                    <tfoot>
                        <tr class="bg-indigo-50 border-t border-indigo-100">
                            <td class="px-4 py-3 text-sm font-semibold text-indigo-900" colspan="4">Total Amount Due</td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-indigo-900">${{ number_format($payment->amount_due, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @endif

        <dl class="px-6 py-5 grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Website</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payment->website->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Items</dt>
                <dd class="mt-1 flex flex-wrap gap-1">
                    @forelse($payment->lineItems as $item)
                        <span class="text-xs px-2 py-0.5 rounded font-medium
                            {{ $item->item_type === 'domain' ? 'bg-purple-100 text-purple-700' :
                               ($item->item_type === 'email' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($item->item_type) }}
                        </span>
                    @empty
                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-blue-100 text-blue-700">
                            {{ ucfirst($payment->payment_type ?? 'website') }}
                        </span>
                    @endforelse
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Method</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payment->payment_method }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ optional($payment->payment_date)->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payment->currency }}</dd>
            </div>
            @if($payment->transaction_id)
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900 font-mono">{{ $payment->transaction_id }}</dd>
            </div>
            @endif
        </dl>
        @if($payment->notes)
        <div class="px-6 pb-5 border-t border-gray-100 pt-4">
            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</dt>
            <dd class="text-sm text-gray-700">{{ $payment->notes }}</dd>
        </div>
        @endif
    </div>

</div>
@endsection
