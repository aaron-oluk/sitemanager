@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('domains.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Domain
            </a>
            <a href="{{ route('websites.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Website
            </a>
        </div>
    </div>

    {{-- Urgent alerts --}}
    @if($expiringDomains->count() > 0 || $expiringEmails->count() > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <div class="shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-amber-800">Action required — renewals due soon</h3>
                <div class="mt-2 space-y-1">
                    @foreach($expiringDomains as $d)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-amber-700">
                                <span class="font-medium">{{ $d->domain_name }}</span>
                                <span class="text-amber-600 ml-1">(domain)</span>
                            </span>
                            <a href="{{ route('domains.show', $d) }}" class="text-amber-800 font-medium hover:underline">
                                expires {{ $d->expiry_date->format('M j') }} · {{ now()->diffInDays($d->expiry_date) }}d →
                            </a>
                        </div>
                    @endforeach
                    @foreach($expiringEmails as $e)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-amber-700">
                                <span class="font-medium">{{ $e->email_address }}</span>
                                <span class="text-amber-600 ml-1">(email)</span>
                            </span>
                            <a href="{{ route('emails.show', $e) }}" class="text-amber-800 font-medium hover:underline">
                                renews {{ $e->renewal_date->format('M j') }} · {{ now()->diffInDays($e->renewal_date) }}d →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Websites</p>
                <div class="h-8 w-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_websites'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['active_websites'] }} active</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenue</p>
                <div class="h-8 w-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_revenue'], 0) }}</p>
            <p class="text-xs text-gray-500 mt-1">${{ number_format($stats['monthly_revenue'], 0) }} this month</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Domains</p>
                <div class="h-8 w-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_domains'] }}</p>
            <p class="text-xs {{ $stats['expiring_domains'] > 0 ? 'text-amber-600 font-medium' : 'text-gray-500' }} mt-1">
                {{ $stats['expiring_domains'] > 0 ? $stats['expiring_domains'] . ' expiring soon' : 'all good' }}
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Emails</p>
                <div class="h-8 w-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_emails'] }}</p>
            <p class="text-xs text-gray-500 mt-1">${{ number_format($stats['monthly_email_cost'], 2) }}/mo cost</p>
        </div>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent websites --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Recent Websites</h2>
                <a href="{{ route('websites.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($websites as $website)
                <a href="{{ route('websites.show', $website) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <div class="h-9 w-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $website->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $website->domain_name }} · {{ $website->client_name }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $website->formatted_amount }}</p>
                        <span class="inline-block text-xs px-1.5 py-0.5 rounded-full {{ $website->status === 'active' ? 'bg-green-100 text-green-700' : ($website->status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($website->status) }}</span>
                    </div>
                </a>
                @empty
                <div class="px-5 py-8 text-center text-sm text-gray-500">
                    No websites yet.
                    <a href="{{ route('websites.create') }}" class="text-indigo-600 hover:underline ml-1">Add one →</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent payments --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Recent Payments</h2>
                <a href="{{ route('payments.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPayments as $payment)
                <a href="{{ route('payments.show', $payment) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                    <div class="h-9 w-9 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $payment->website->name ?? ($payment->domain->domain_name ?? 'N/A') }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->payment_method }} · {{ optional($payment->payment_date)->format('M j, Y') }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-gray-900">{{ $payment->formatted_amount }}</p>
                        <p class="text-xs text-gray-400">{{ $payment->formatted_usd_equivalent }}</p>
                    </div>
                </a>
                @empty
                <div class="px-5 py-8 text-center text-sm text-gray-500">
                    No payments yet.
                    <a href="{{ route('payments.create') }}" class="text-indigo-600 hover:underline ml-1">Record one →</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- All domains summary --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Domains</h2>
                <a href="{{ route('domains.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Manage →</a>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total registered</span>
                    <span class="font-semibold text-gray-900">{{ $stats['total_domains'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Annual cost</span>
                    <span class="font-semibold text-gray-900">${{ number_format($stats['total_domain_cost'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Expiring in 30 days</span>
                    <span class="font-semibold {{ $stats['expiring_domains'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $stats['expiring_domains'] }}</span>
                </div>
                <a href="{{ route('domains.create') }}" class="mt-1 block w-full text-center text-xs text-indigo-600 hover:text-indigo-800 font-medium pt-2 border-t border-gray-100">
                    + Register domain
                </a>
            </div>
        </div>

        {{-- Email summary --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Email Hosting</h2>
                <a href="{{ route('emails.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Manage →</a>
            </div>
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total accounts</span>
                    <span class="font-semibold text-gray-900">{{ $stats['total_emails'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Active plans</span>
                    <span class="font-semibold text-gray-900">{{ $stats['active_email_plans'] }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Monthly base cost</span>
                    <span class="font-semibold text-gray-900">${{ number_format($stats['monthly_email_cost'], 2) }}</span>
                </div>
                <a href="{{ route('emails.create') }}" class="mt-1 block w-full text-center text-xs text-indigo-600 hover:text-indigo-800 font-medium pt-2 border-t border-gray-100">
                    + Add email account
                </a>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="px-5 py-4 space-y-2">
                <a href="{{ route('websites.create') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-700">
                    <div class="h-7 w-7 bg-blue-50 rounded flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    Add new website
                </a>
                <a href="{{ route('payments.create') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-700">
                    <div class="h-7 w-7 bg-green-50 rounded flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    Record payment
                </a>
                <a href="{{ route('domains.create') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-700">
                    <div class="h-7 w-7 bg-purple-50 rounded flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    Register domain
                </a>
                <a href="{{ route('emails.create') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors text-sm text-gray-700">
                    <div class="h-7 w-7 bg-orange-50 rounded flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    Add email account
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
