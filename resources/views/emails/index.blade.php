@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Email Accounts</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $totalEmails }} accounts · {{ $activeEmails }} active</p>
        </div>
        <a href="{{ route('emails.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Email
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalEmails }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total accounts</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $activeEmails }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Active</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">${{ number_format($totalMonthlyCost, 2) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Billed total</p>
        </div>
    </div>

    {{-- Grouped by domain --}}
    @if($emailsByDomain->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <p class="text-gray-500 text-sm mb-3">No email accounts yet.</p>
            <a href="{{ route('emails.create') }}" class="text-indigo-600 hover:underline text-sm font-medium">Add your first account →</a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($emailsByDomain as $cluster)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Domain header --}}
                <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                {{ $cluster['domain'] ? $cluster['domain']->domain_name : 'No domain assigned' }}
                            </h3>
                            <p class="text-xs text-gray-500">{{ $cluster['count'] }} account{{ $cluster['count'] === 1 ? '' : 's' }} · ${{ number_format($cluster['total_cost'], 2) }} billed</p>
                        </div>
                    </div>
                    @if($cluster['domain'])
                        <a href="{{ route('domains.show', $cluster['domain']) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View domain →</a>
                    @endif
                </div>

                {{-- Email rows --}}
                <table class="min-w-full divide-y divide-gray-50">
                    <thead>
                        <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            <th class="px-5 py-2.5 text-left">Email</th>
                            <th class="px-5 py-2.5 text-left">Provider</th>
                            <th class="px-5 py-2.5 text-left">Plan</th>
                            <th class="px-5 py-2.5 text-right">Billed</th>
                            <th class="px-5 py-2.5 text-left">Status</th>
                            <th class="px-5 py-2.5 text-left">Renews</th>
                            <th class="px-5 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($cluster['emails'] as $email)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $email->email_address }}</p>
                                @if($email->website)
                                    <p class="text-xs text-gray-400">{{ $email->website->name }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $email->provider }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ ucfirst($email->hosting_plan ?? '—') }}</td>
                            <td class="px-5 py-3 text-sm font-semibold text-gray-900 text-right">{{ $email->formatted_billing_total_cost }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $email->status === 'active' ? 'bg-green-100 text-green-700' :
                                       ($email->status === 'inactive' ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($email->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm {{ $email->isExpiringSoon() ? 'text-amber-600 font-medium' : 'text-gray-600' }}">
                                {{ optional($email->renewal_date)->format('M j, Y') }}
                                @if($email->isExpiringSoon())
                                    <span class="block text-xs font-normal">{{ now()->diffInDays($email->renewal_date) }}d left</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3 text-sm">
                                    <a href="{{ route('emails.show', $email) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                    <a href="{{ route('emails.edit', $email) }}" class="text-gray-500 hover:text-gray-700">Edit</a>
                                    <form action="{{ route('emails.destroy', $email) }}" method="POST" class="inline" onsubmit="return confirm('Delete this email account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
