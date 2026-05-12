@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">Create Payment</h1>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payments.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="website_id" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                        <select name="website_id" id="website_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Website</option>
                            @foreach($websites as $id => $name)
                                <option value="{{ $id }}" {{ old('website_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" id="currency" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ old('currency', $currencies[0] ?? 'USD') == $currency ? 'selected' : '' }}>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <input type="text" name="payment_method" id="payment_method" value="{{ old('payment_method') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                        <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date (Auto)</label>
                        <input type="date" id="payment_date" value="{{ now()->format('Y-m-d') }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" />
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('payments.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Payment</button>
                </div>
            </form>
        </div>
    </div>
@endsection