@extends('layouts.app')

@section('title', 'Dashboard - SiteManager')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
        <div class="flex space-x-3">
            <a href="{{ route('websites.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Website
            </a>
            <a href="{{ route('payments.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Record Payment
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                        <p class="text-blue-100">Here's what's happening with your web development business today.</p>
                    </div>
                    <div class="hidden md:block">
                        <svg class="w-24 h-24 text-blue-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Websites -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Websites</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_websites'] }}</p>
                            <p class="text-sm text-green-600 mt-1">
                                <span class="font-medium">+{{ $stats['active_websites'] }}</span> active
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_revenue'], 2) }}</p>
                            <p class="text-sm text-green-600 mt-1">
                                <span class="font-medium">${{ number_format($stats['monthly_revenue'], 2) }}</span> this month
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Domains -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Domains</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_domains'] }}</p>
                            <p class="text-sm text-orange-600 mt-1">
                                <span class="font-medium">{{ $stats['expiring_domains'] }}</span> expiring soon
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Email Accounts -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Email Accounts</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_emails'] }}</p>
                            <p class="text-sm text-blue-600 mt-1">
                                <span class="font-medium">${{ number_format($stats['monthly_email_cost'], 2) }}</span> monthly cost
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('websites.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">Add New Website</span>
                        </a>
                        <a href="{{ route('payments.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">Record Payment</span>
                        </a>
                        <a href="{{ route('domains.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">Add Domain</span>
                        </a>
                        <a href="{{ route('emails.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">Setup Email</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        @forelse($websites as $website)
                        <div class="flex items-center p-3 rounded-lg bg-gray-50">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $website->name }}</p>
                                <p class="text-xs text-gray-500">Deployed {{ $website->deployment_date->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No recent websites</p>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Renewals -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Renewals</h3>
                    <div class="space-y-3">
                        @forelse($domains->where('expiry_date', '<=', now()->addDays(30)) as $domain)
                        <div class="flex items-center p-3 rounded-lg bg-orange-50 border border-orange-100">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $domain->domain_name }}</p>
                                <p class="text-xs text-orange-600">Expires {{ $domain->expiry_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No upcoming renewals</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Websites -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Websites</h3>
                            <a href="{{ route('websites.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse($websites->take(5) as $website)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $website->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $website->domain_name }}</p>
                                    @if($website->domain_id && $website->domainRelation && is_object($website->domainRelation))
                                        <p class="text-xs text-blue-600">
                                            Domain expires: {{ optional($website->domainRelation->expiry_date)->format('M d, Y') }}
                                            @if($website->domainRelation->isExpiringSoon())
                                                <span class="text-orange-600 font-medium">({{ $website->domainRelation->days_until_expiry }} days)</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $website->status }}</p>
                                <p class="text-xs text-gray-500">{{ optional($website->deployment_date)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-600">{{ $website->formatted_amount }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                            <p class="text-gray-500">No websites yet</p>
                            <a href="{{ route('websites.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Add your first website</a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                            <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @forelse($recentPayments->take(5) as $payment)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $payment->website->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment->payment_method }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $payment->formatted_amount }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->formatted_usd_equivalent }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <p class="text-gray-500">No payments yet</p>
                            <a href="{{ route('payments.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Record your first payment</a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Additional Features Section -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Domain Management -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Domain Management</h3>
                        <a href="{{ route('domains.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage All</a>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <span class="text-sm text-gray-700">Total Domains</span>
                            <span class="font-semibold text-purple-600">{{ $stats['total_domains'] }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <span class="text-sm text-gray-700">Expiring Soon</span>
                            <span class="font-semibold text-orange-600">{{ $stats['expiring_domains'] }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm text-gray-700">Annual Cost</span>
                            <span class="font-semibold text-blue-600">${{ number_format($stats['total_domain_cost'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Email Management -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Email Management</h3>
                        <a href="{{ route('emails.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage All</a>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                            <span class="text-sm text-gray-700">Total Accounts</span>
                            <span class="font-semibold text-orange-600">{{ $stats['total_emails'] }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <span class="text-sm text-gray-700">Monthly Cost</span>
                            <span class="font-semibold text-red-600">${{ number_format($stats['monthly_email_cost'], 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-sm text-gray-700">Active Plans</span>
                            <span class="font-semibold text-green-600">{{ $stats['active_email_plans'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
