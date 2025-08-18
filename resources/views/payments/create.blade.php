<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Record Payment') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ms-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payments.store') }}" class="bg-white rounded-2xl shadow border border-gray-100 p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website</label>
                        <select name="website_id" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach($websites as $id => $name)
                                <option value="{{ $id }}" {{ old('website_id')==$id?'selected':'' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <input name="payment_method" value="{{ old('payment_method') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Transaction ID (optional)</label>
                        <input name="transaction_id" value="{{ old('transaction_id') }}" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                        <input type="date" name="payment_date" value="{{ old('payment_date') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach(['completed','pending','failed'] as $status)
                                <option value="{{ $status }}" {{ old('status')===$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('payments.index') }}" class="px-4 py-2 rounded-lg border">Cancel</a>
                    <button class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


