<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Website') }}</h2>
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

            <form method="POST" action="{{ route('websites.store') }}" class="bg-white rounded-2xl shadow border border-gray-100 p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website Name</label>
                        <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain</label>
                        <input name="domain" value="{{ old('domain') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="example.com" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Link to Existing Domain (Optional)</label>
                        <select name="domain_id" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Create New Domain --</option>
                            @foreach($domains as $id => $domainName)
                                <option value="{{ $id }}" {{ old('domain_id') == $id ? 'selected' : '' }}>{{ $domainName }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select an existing domain to link, or leave empty to create a new one</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Host Server</label>
                        <input name="host_server" value="{{ old('host_server') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deployment Date</label>
                        <input type="date" name="deployment_date" value="{{ old('deployment_date') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                        <input type="number" step="0.01" name="amount_paid" value="{{ old('amount_paid') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <select name="currency" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency }}" {{ old('currency')==$currency?'selected':'' }}>{{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach(['active','inactive','maintenance'] as $status)
                                <option value="{{ $status }}" {{ old('status')===$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Name</label>
                        <input name="client_name" value="{{ old('client_name') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Client Email</label>
                        <input type="email" name="client_email" value="{{ old('client_email') }}" required class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="4" class="mt-1 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('websites.index') }}" class="px-4 py-2 rounded-lg border">Cancel</a>
                    <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


