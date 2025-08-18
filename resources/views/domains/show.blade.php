<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $domain->domain_name }}</h2>
            <a href="{{ route('domains.edit', $domain) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Edit</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm text-gray-500">Registrar</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $domain->registrar }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Registration Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($domain->registration_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($domain->expiry_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Annual Cost</dt>
                        <dd class="mt-1 text-gray-900 font-medium">${{ number_format($domain->annual_cost, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1"><span class="px-2 py-1 rounded-full text-xs {{ $domain->status === 'active' ? 'bg-green-100 text-green-700' : ($domain->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($domain->status) }}</span></dd>
                    </div>
                </dl>
                @if($domain->notes)
                    <div class="mt-6">
                        <dt class="text-sm text-gray-500">Notes</dt>
                        <dd class="mt-1 text-gray-900">{{ $domain->notes }}</dd>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>


