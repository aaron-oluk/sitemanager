@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('emails.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $email->email_address }}</h1>
                @if($email->provider)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $email->provider }}</p>
                @endif
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $email->status === 'active' ? 'bg-green-100 text-green-700' :
                   ($email->status === 'suspended' ? 'bg-yellow-100 text-yellow-700' :
                   ($email->status === 'inactive' ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-700')) }}">
                {{ ucfirst($email->status) }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('emails.edit', $email) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form action="{{ route('emails.destroy', $email) }}" method="POST" onsubmit="return confirm('Delete this email account?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-sm font-medium text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Expiry warning --}}
    @if($email->isExpiringSoon())
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-medium text-amber-800">
            Renewal due in {{ now()->diffInDays($email->renewal_date) }} days — {{ optional($email->renewal_date)->format('M j, Y') }}.
        </p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main info --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Account info --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Account Info</h2>
                </div>
                <dl class="px-5 py-4 grid grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $email->provider }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hosting Plan</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ ucfirst($email->hosting_plan ?? '—') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ optional($email->start_date)->format('M j, Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Renewal Date</dt>
                        <dd class="mt-1 text-sm font-medium {{ $email->isExpiringSoon() ? 'text-amber-600' : 'text-gray-900' }}">
                            {{ optional($email->renewal_date)->format('M j, Y') ?? '—' }}
                        </dd>
                    </div>
                    @if($email->website)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Website</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            <a href="{{ route('websites.show', $email->website) }}" class="text-indigo-600 hover:text-indigo-800">{{ $email->website->name }}</a>
                        </dd>
                    </div>
                    @endif
                    @if($email->domain)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">
                            <a href="{{ route('domains.show', $email->domain) }}" class="text-indigo-600 hover:text-indigo-800">{{ $email->domain->domain_name }}</a>
                        </dd>
                    </div>
                    @endif
                </dl>
                @if($email->notes)
                <div class="px-5 pb-4 border-t border-gray-50 pt-3">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notes</p>
                    <p class="text-sm text-gray-700">{{ $email->notes }}</p>
                </div>
                @endif
            </div>

            {{-- Billing breakdown --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Billing Breakdown</h2>
                </div>
                <div class="px-5 py-4 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Monthly cost</span>
                        <span>${{ number_format($email->monthly_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Plan duration</span>
                        <span>{{ $email->billing_duration_months }} month{{ $email->billing_duration_months === 1 ? '' : 's' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ $email->formatted_billing_subtotal }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax (18%)</span>
                        <span>{{ $email->formatted_billing_tax_amount }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Transaction fee (2.5%)</span>
                        <span>{{ $email->formatted_billing_transaction_fee }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900 pt-2 border-t border-gray-100">
                        <span>Total</span>
                        <span>{{ $email->formatted_billing_total_cost }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Summary</h2>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500">Total billed</p>
                        <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $email->formatted_billing_total_cost }}</p>
                    </div>
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Monthly rate</p>
                        <p class="text-lg font-semibold text-gray-900 mt-0.5">${{ number_format($email->monthly_cost, 2) }}/mo</p>
                    </div>
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Plan</p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ ucfirst($email->hosting_plan ?? '—') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $email->billing_duration_months }} month{{ $email->billing_duration_months === 1 ? '' : 's' }} cycle</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
