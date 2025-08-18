<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Email Account') }}</h2>
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

            <form method="POST" action="{{ route('emails.store') }}" class="bg-white rounded-2xl shadow border border-gray-100 p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input name="email_address" value="{{ old('email_address') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="user@domain.com" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain (Auto-detected)</label>
                        <select name="domain_id" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Auto-detect from email --</option>
                            @foreach($domains as $id => $domainName)
                                <option value="{{ $id }}" {{ old('domain_id') == $id ? 'selected' : '' }}>{{ $domainName }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Domain will be auto-detected from email address if not selected</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provider</label>
                        <input name="provider" value="{{ old('provider') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hosting Plan</label>
                        <input name="hosting_plan" value="{{ old('hosting_plan') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Monthly Cost</label>
                        <input type="number" step="0.01" name="monthly_cost" value="{{ old('monthly_cost') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Associated Website (Optional)</label>
                        <select name="associated_website" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- No website association --</option>
                            @foreach($websites as $id => $websiteName)
                                <option value="{{ $id }}" {{ old('associated_website') == $id ? 'selected' : '' }}>{{ $websiteName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Renewal Date</label>
                        <input type="date" name="renewal_date" value="{{ old('renewal_date') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach(['active','inactive','suspended'] as $status)
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
                    <a href="{{ route('emails.index') }}" class="px-4 py-2 rounded-lg border">Cancel</a>
                    <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


