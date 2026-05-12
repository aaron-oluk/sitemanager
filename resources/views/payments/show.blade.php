@extends('layouts.app')

@section('content')
<div class="p-6 space-y-5 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold text-gray-900">Payment</h1>
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($payment->status === 'pending'   ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                @if($payment->receipt_number)
                    <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $payment->receipt_number }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.receipt', $payment) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Receipt
            </a>
            <a href="{{ route('payments.download-receipt', $payment) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
            <a href="{{ route('payments.edit', $payment) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
                Edit
            </a>
        </div>
    </div>

    {{-- Amount summary --}}
    @php $balance = (float)$payment->amount_due - (float)$payment->amount; @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 col-span-1">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Amount Paid</p>
            <p class="text-3xl font-bold text-gray-900">{{ $payment->formatted_amount }}</p>
            @if($payment->currency !== 'USD')
                <p class="text-xs text-gray-400 mt-1">≈ {{ $payment->formatted_usd_equivalent }}</p>
            @else
                <p class="text-xs text-gray-300 mt-1">{{ $payment->currency }}</p>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 col-span-1">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Amount Due</p>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($payment->amount_due, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">USD</p>
        </div>

        <div class="rounded-xl border shadow-sm px-5 py-4 col-span-1 flex flex-col justify-between
            {{ $balance <= 0 ? 'bg-green-50 border-green-200' : 'bg-amber-50 border-amber-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wider mb-1
                {{ $balance <= 0 ? 'text-green-500' : 'text-amber-500' }}">
                Balance
            </p>
            @if($balance <= 0)
                <div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <p class="text-xl font-bold text-green-700">Paid in full</p>
                    </div>
                    <p class="text-xs text-green-500 mt-1">No outstanding balance</p>
                </div>
            @else
                <div>
                    <p class="text-3xl font-bold text-amber-700">${{ number_format($balance, 2) }}</p>
                    <p class="text-xs text-amber-500 mt-1">Outstanding</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Line items --}}
    @if($payment->lineItems->count())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Items &amp; Cost Breakdown</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/2">Item</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit Cost</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tax (18%)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Fee (2.5%)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($payment->lineItems as $item)
                @php
                    $sym = ['USD'=>'$','UGX'=>'USh ','EUR'=>'€','GBP'=>'£','KES'=>'KSh ','TZS'=>'TSh ','NGN'=>'₦'][$item->currency] ?? ($item->currency . ' ');
                    $dec = in_array($item->currency, ['UGX','TZS']) ? 0 : 2;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-gray-900">{{ $item->label }}</p>
                        <span class="inline-block mt-0.5 text-xs px-1.5 py-0.5 rounded font-medium
                            {{ $item->item_type === 'domain' ? 'bg-purple-100 text-purple-600' :
                               ($item->item_type === 'email'  ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }}">
                            {{ ucfirst($item->item_type) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right text-gray-600">{{ $sym }}{{ number_format($item->unit_cost, $dec) }}</td>
                    <td class="px-5 py-3.5 text-right text-gray-600">{{ $sym }}{{ number_format($item->tax_amount, $dec) }}</td>
                    <td class="px-5 py-3.5 text-right text-gray-600">{{ $sym }}{{ number_format($item->transaction_fee, $dec) }}</td>
                    <td class="px-5 py-3.5 text-right font-semibold text-gray-900">{{ $sym }}{{ number_format($item->total_amount, $dec) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-gray-200 bg-gray-50">
                    <td class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" colspan="4">Total Amount Due</td>
                    <td class="px-5 py-3 text-right font-bold text-gray-900">${{ number_format($payment->amount_due, 2) }}</td>
                </tr>
                <tr class="border-t border-gray-100">
                    <td class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider" colspan="4">Amount Paid</td>
                    <td class="px-5 py-3 text-right font-bold text-gray-900">{{ $payment->formatted_amount }}</td>
                </tr>
                @if($balance > 0)
                <tr class="border-t border-amber-100 bg-amber-50">
                    <td class="px-5 py-3 text-xs font-semibold text-amber-600 uppercase tracking-wider" colspan="4">Outstanding Balance</td>
                    <td class="px-5 py-3 text-right font-bold text-amber-700">${{ number_format($balance, 2) }}</td>
                </tr>
                @else
                <tr class="border-t border-green-100 bg-green-50">
                    <td class="px-5 py-3 text-xs font-semibold text-green-600 uppercase tracking-wider" colspan="4">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Paid in Full
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right font-bold text-green-700">$0.00</td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>
    @endif

    {{-- Payment info --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Payment Information</h2>
        </div>
        <dl class="px-5 py-4 grid grid-cols-2 gap-x-10 gap-y-4">
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Website</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $payment->website->name ?? 'N/A' }}</dd>
                @if($payment->website?->domain)
                    <dd class="text-xs text-gray-400">{{ $payment->website->domain }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Payment Method</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $payment->payment_method }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date</dt>
                <dd class="text-sm font-medium text-gray-900">{{ optional($payment->payment_date)->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Currency</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $payment->currency }}</dd>
            </div>
            @if($payment->transaction_id)
            <div class="col-span-2">
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Transaction ID</dt>
                <dd class="text-sm font-medium text-gray-900 font-mono">{{ $payment->transaction_id }}</dd>
            </div>
            @endif
        </dl>
        @if($payment->notes)
        <div class="px-5 pb-4 border-t border-gray-100 pt-4">
            <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Notes</dt>
            <dd class="text-sm text-gray-700">{{ $payment->notes }}</dd>
        </div>
        @endif
    </div>

</div>
@endsection
