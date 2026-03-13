@extends('layouts.app')

@section('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payment Details') }}</h2>
            <a href="{{ route('payments.edit', $payment) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">Edit</a>
        </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow border border-gray-100 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm text-gray-500">Website</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $payment->website->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Amount</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $payment->formatted_amount }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">USD Equivalent</dt>
                        <dd class="mt-1 text-gray-600">{{ $payment->formatted_usd_equivalent }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Currency</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $payment->currency }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Method</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $payment->payment_method }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($payment->payment_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1"><span class="px-2 py-1 rounded-full text-xs {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($payment->status) }}</span></dd>
                    </div>
                    @if($payment->transaction_id)
                    <div>
                        <dt class="text-sm text-gray-500">Transaction ID</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $payment->transaction_id }}</dd>
                    </div>
                    @endif
                </dl>
                @if($payment->notes)
                    <div class="mt-6">
                        <dt class="text-sm text-gray-500">Notes</dt>
                        <dd class="mt-1 text-gray-900">{{ $payment->notes }}</dd>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


