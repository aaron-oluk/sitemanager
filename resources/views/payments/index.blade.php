@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payments') }}</h2>
            <a href="{{ route('payments.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">Record Payment</a>
        </div>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded shadow border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USD Equivalent</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $payment->website->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->payment_method }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($payment->payment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-semibold">
                                        {{ $payment->formatted_amount }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $payment->formatted_usd_equivalent }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($payment->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        <a href="{{ route('payments.edit', $payment) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Delete this payment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $payments->links() }}</div>
            </div>
        </div>
    </div>
@endsection


