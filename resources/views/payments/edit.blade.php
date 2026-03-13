@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Payment') }}</h2>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ms-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payments.update', $payment) }}" class="bg-white rounded shadow border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website</label>
                        <select name="website_id" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach($websites as $id => $name)
                                <option value="{{ $id }}" {{ old('website_id', $payment->website_id)==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <input name="payment_method" value="{{ old('payment_method', $payment->payment_method) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <select name="currency" id="currency" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ old('currency', $payment->currency)==$currency?'selected':'' }}>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">USD Equivalent</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-sm text-sm text-gray-600" id="usd-equivalent">
                            {{ $payment->formatted_usd_equivalent }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transaction ID (optional)</label>
                        <input name="transaction_id" value="{{ old('transaction_id', $payment->transaction_id) }}" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input type="date" name="payment_date" value="{{ old('payment_date', optional($payment->payment_date)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach(['completed','pending','failed'] as $status)
                                <option value="{{ $status }}" {{ old('status', $payment->status)===$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-blue-500">{{ old('notes', $payment->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('payments.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    <button class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Currency conversion rates (simplified - in real app, these would come from the server)
        const exchangeRates = {
            'USD': 1.0,
            'UGX': 0.00027,
            'EUR': 1.08,
            'GBP': 1.26,
            'KES': 0.0069,
            'TZS': 0.00039,
            'NGN': 0.00066,
        };

        function updateUsdEquivalent() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const currency = document.getElementById('currency').value;
            const rate = exchangeRates[currency] || 1.0;
            const usdAmount = amount * rate;
            
            document.getElementById('usd-equivalent').textContent = '$' + usdAmount.toFixed(2);
        }

        document.getElementById('amount').addEventListener('input', updateUsdEquivalent);
        document.getElementById('currency').addEventListener('change', updateUsdEquivalent);
        
        // Initialize on page load
        updateUsdEquivalent();
    </script>
@endsection


