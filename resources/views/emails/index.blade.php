@extends('layouts.app')

@section('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Email Management') }}</h2>
            <a href="{{ route('emails.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Add Email</a>
        </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                    <p class="text-sm text-gray-600">Total Emails</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEmails }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                    <p class="text-sm text-gray-600">Monthly Cost</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalMonthlyCost, 2) }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                    <p class="text-sm text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeEmails }}</p>
                </div>
            </div>

            <!-- Emails Clustered by Domain -->
            <div class="space-y-6">
                @foreach($emailsByDomain as $domainId => $cluster)
                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($cluster['domain'])
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $cluster['domain']->domain_name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $cluster['count'] }} email(s) • ${{ number_format($cluster['total_cost'], 2) }}/month</p>
                                    @else
                                        <h3 class="text-lg font-semibold text-gray-900">Unassigned Domain</h3>
                                        <p class="text-sm text-gray-600">{{ $cluster['count'] }} email(s) • ${{ number_format($cluster['total_cost'], 2) }}/month</p>
                                    @endif
                                </div>
                                @if($cluster['domain'])
                                    <a href="{{ route('domains.show', $cluster['domain']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Domain</a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Renewal</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($cluster['emails'] as $email)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div>
                                                    @if($email->email_address)
                                                        <p class="text-sm font-medium text-gray-900">{{ $email->email_address }}</p>
                                                    @else
                                                        <p class="text-sm font-medium text-gray-900 text-gray-500">No email address</p>
                                                    @endif
                                                    @if($email->website)
                                                        <p class="text-xs text-blue-600">{{ $email->website->name }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $email->provider }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $email->hosting_plan }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ $email->formatted_monthly_cost }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs {{ $email->status === 'active' ? 'bg-green-100 text-green-700' : ($email->status === 'inactive' ? 'bg-gray-100 text-gray-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($email->status) }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                @if($email->isExpiringSoon())
                                                    <span class="text-orange-600 font-medium">
                                                        {{ $email->renewal_date->format('M d, Y') }}
                                                        ({{ $email->days_until_renewal }} days)
                                                    </span>
                                                @else
                                                    {{ optional($email->renewal_date)->format('M d, Y') }}
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                                <a href="{{ route('emails.show', $email) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                                <a href="{{ route('emails.edit', $email) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                                <form action="{{ route('emails.destroy', $email) }}" method="POST" class="inline" onsubmit="return confirm('Delete this email?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination for backward compatibility -->
            @if($emails->hasPages())
                <div class="mt-6">
                    {{ $emails->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection 