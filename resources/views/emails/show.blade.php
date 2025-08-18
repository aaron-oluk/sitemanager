<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $email->email_address }}</h2>
            <a href="{{ route('emails.edit', $email) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Edit</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm text-gray-500">Provider</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $email->provider }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Hosting Plan</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $email->hosting_plan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Monthly Cost</dt>
                        <dd class="mt-1 text-gray-900 font-medium">${{ number_format($email->monthly_cost, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($email->start_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Renewal Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($email->renewal_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1"><span class="px-2 py-1 rounded-full text-xs {{ $email->status === 'active' ? 'bg-green-100 text-green-700' : ($email->status === 'suspended' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($email->status) }}</span></dd>
                    </div>
                </dl>
                @if($email->associated_website || $email->notes)
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm text-gray-500">Associated Website</dt>
                            <dd class="mt-1 text-gray-900">{{ $email->associated_website ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Notes</dt>
                            <dd class="mt-1 text-gray-900">{{ $email->notes ?? '-' }}</dd>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


