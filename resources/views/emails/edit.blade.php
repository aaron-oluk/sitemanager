@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Email Account') }}</h2>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ms-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('emails.update', $email) }}"
                class="bg-white rounded shadow border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email_address" value="{{ old('email_address', $email->email_address) }}"
                            required class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain</label>
                        <select name="domain_id"
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- No domain association --</option>
                            @foreach ($domains as $id => $domainName)
                                <option value="{{ $id }}"
                                    {{ old('domain_id', $email->domain_id) == $id ? 'selected' : '' }}>{{ $domainName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provider</label>
                        <input name="provider" value="{{ old('provider', $email->provider) }}" required
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hosting Plan</label>
                        <select id="hosting_plan" name="hosting_plan"
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500"
                            onchange="updateCostLabel(this)">
                            @foreach (\App\Models\Email::getHostingPlanOptions() as $value => $label)
                                <option value="{{ $value }}"
                                    data-months="{{ ['monthly' => 1, 'quarterly' => 3, 'biannual' => 6, 'annual' => 12, 'biennial' => 24, 'triennial' => 36][$value] }}"
                                    {{ old('hosting_plan', $email->hosting_plan) == $value ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label id="cost_label" class="block text-sm font-medium text-gray-700">Monthly Cost</label>
                        <input type="number" step="0.01" name="monthly_cost"
                            value="{{ old('monthly_cost', $email->monthly_cost) }}" required
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Associated Website</label>
                        <select name="website_id"
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- No website association --</option>
                            @foreach ($websites as $id => $websiteName)
                                <option value="{{ $id }}"
                                    {{ old('website_id', $email->website_id) == $id ? 'selected' : '' }}>
                                    {{ $websiteName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date"
                            value="{{ old('start_date', optional($email->start_date)->format('Y-m-d')) }}" required
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Renewal Date</label>
                        <input type="date" name="renewal_date"
                            value="{{ old('renewal_date', optional($email->renewal_date)->format('Y-m-d')) }}" required
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status"
                            class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">
                            @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended', 'pending' => 'Pending', 'cancelled' => 'Cancelled'] as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('status', $email->status) == $value ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500">{{ old('notes', $email->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('emails.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    <button class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function updateCostLabel(select) {
            const months = select.options[select.selectedIndex].dataset.months;
            const label = document.getElementById('cost_label');
            label.textContent = 'Monthly Cost (' + months + (months == 1 ? ' month)' : ' months)');
        }
        document.addEventListener('DOMContentLoaded', function() {
            updateCostLabel(document.getElementById('hosting_plan'));
        });
    </script>
@endsection
