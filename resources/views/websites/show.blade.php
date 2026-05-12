@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-4xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('websites.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $website->name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $website->domain_name }}</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $website->status === 'active' ? 'bg-green-100 text-green-700' :
                   ($website->status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                {{ ucfirst($website->status) }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('websites.edit', $website) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form action="{{ route('websites.destroy', $website) }}" method="POST" onsubmit="return confirm('Delete this website?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-sm font-medium text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Client & project --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Project Details</h2>
                </div>
                <dl class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $website->client_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Client Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $website->client_email }}" class="text-indigo-600 hover:underline">{{ $website->client_email }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Host Server</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $website->host_server }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Deployed</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ optional($website->deployment_date)->format('M j, Y') }}</dd>
                    </div>
                    @if($website->description)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</dt>
                        <dd class="mt-1 text-sm text-gray-700">{{ $website->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Domain info --}}
            @if($website->domain_id && $website->domainRelation)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Domain</h2>
                    <a href="{{ route('domains.show', $website->domain_id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View domain →</a>
                </div>
                <dl class="px-5 py-4 grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $website->domainRelation->domain_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ optional($website->domainRelation->registration_date)->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</dt>
                        <dd class="mt-1 text-sm {{ $website->domainRelation->isExpiringSoon() ? 'text-amber-600 font-semibold' : 'text-gray-900' }}">
                            {{ optional($website->domainRelation->expiry_date)->format('M j, Y') }}
                            @if($website->domainRelation->isExpiringSoon())
                                <span class="block text-xs font-normal">⚠ {{ now()->diffInDays($website->domainRelation->expiry_date) }} days left</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            {{-- Domain cost breakdown --}}
            @if($website->domain_purchased)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Domain Cost Breakdown</h2>
                </div>
                <div class="px-5 py-4">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Base cost</span>
                            <span>{{ $website->formatted_domain_base_cost }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (18%)</span>
                            <span>{{ $website->formatted_domain_tax_amount }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Transaction fee (2.5%)</span>
                            <span>{{ $website->formatted_domain_transaction_fee }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-100">
                            <span>Total</span>
                            <span>{{ $website->formatted_domain_total_cost }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payments --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Payments</h2>
                    <a href="{{ route('payments.create') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">+ Record</a>
                </div>
                @if($website->payments->isEmpty())
                    <p class="px-5 py-6 text-sm text-gray-500 text-center">No payments recorded.</p>
                @else
                <div class="divide-y divide-gray-50">
                    @foreach($website->payments as $payment)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $payment->formatted_amount }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->payment_method }} · {{ optional($payment->payment_date)->format('M j, Y') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-400">{{ $payment->formatted_usd_equivalent }}</span>
                            <a href="{{ route('payments.show', $payment) }}" class="text-xs text-indigo-600 hover:text-indigo-800">View</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Financials</h2>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Amount charged</p>
                        <p class="text-xl font-bold text-gray-900 mt-0.5">{{ $website->formatted_amount }}</p>
                        <p class="text-xs text-gray-400">≈ {{ $website->formatted_usd_equivalent }}</p>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Currency</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $website->currency }}</p>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Domain included</p>
                        <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $website->amount_includes_domain ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
