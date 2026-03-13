@extends('layouts.app')

@section('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $domain->domain_name }}</h2>
            <a href="{{ route('domains.edit', $domain) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Edit</a>
        </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow border border-gray-100 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm text-gray-500">Domain Name</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $domain->domain_name }}</dd>
                    </div>
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
                        <dd class="mt-1 text-gray-900 font-medium">
                            @if($domain->isExpiringSoon())
                                <span class="text-orange-600 font-medium">
                                    {{ optional($domain->expiry_date)->format('M d, Y') }} 
                                    ({{ $domain->days_until_expiry }} days)
                                </span>
                            @else
                                {{ optional($domain->expiry_date)->format('M d, Y') }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Annual Cost</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $domain->formatted_annual_cost }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1"><span class="px-2 py-1 rounded-full text-xs {{ $domain->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($domain->status) }}</span></dd>
                    </div>
                </dl>

                @if($domain->website)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h3 class="text-sm font-medium text-blue-900 mb-2">Linked Website</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-900 font-medium">{{ $domain->website->name }}</p>
                            <p class="text-blue-700 text-sm">{{ $domain->website->client_name }}</p>
                        </div>
                        <a href="{{ route('websites.show', $domain->website) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Website →
                        </a>
                    </div>
                </div>
                @endif

                @if($domain->websites->count() > 0)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">All Linked Websites</h3>
                    <div class="space-y-3">
                        @foreach($domain->websites as $website)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                            <div>
                                <p class="font-medium text-gray-900">{{ $website->name }}</p>
                                <p class="text-sm text-gray-600">{{ $website->client_name }}</p>
                            </div>
                            <a href="{{ route('websites.show', $website) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View →
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($domain->notes)
                    <div class="mt-6">
                        <dt class="text-sm text-gray-500">Notes</dt>
                        <dd class="mt-1 text-gray-900">{{ $domain->notes }}</dd>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


