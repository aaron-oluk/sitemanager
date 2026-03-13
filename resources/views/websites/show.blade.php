@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $website->name }}</h2>
            <a href="{{ route('websites.edit', $website) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">Edit</a>
        </div>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded shadow border border-gray-100 p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm text-gray-500">Website Name</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Domain</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->domain_name }}</dd>
                    </div>
                    @if($website->domain_id)
                    <div>
                        <dt class="text-sm text-gray-500">Linked Domain</dt>
                        <dd class="mt-1 text-gray-900 font-medium">
                            <a href="{{ route('domains.show', $website->domain_id) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $website->domainRelation->domain_name ?? 'N/A' }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Domain Registration Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">
                            {{ optional($website->domainRelation->registration_date)->format('M d, Y') ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Domain Expiry Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">
                            @if($website->domainRelation && $website->domainRelation->isExpiringSoon())
                                <span class="text-orange-600 font-medium">
                                    {{ optional($website->domainRelation->expiry_date)->format('M d, Y') }} 
                                    ({{ $website->domainRelation->days_until_expiry }} days)
                                </span>
                            @else
                                {{ optional($website->domainRelation->expiry_date)->format('M d, Y') ?? 'N/A' }}
                            @endif
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm text-gray-500">Host Server</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->host_server }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Deployment Date</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ optional($website->deployment_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Amount Paid</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->formatted_amount }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Currency</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->currency }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">USD Equivalent</dt>
                        <dd class="mt-1 text-gray-600">{{ $website->formatted_usd_equivalent }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="mt-1"><span class="px-2 py-1 rounded-full text-xs {{ $website->status === 'active' ? 'bg-green-100 text-green-700' : ($website->status === 'inactive' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($website->status) }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Client Name</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->client_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Client Email</dt>
                        <dd class="mt-1 text-gray-900 font-medium">{{ $website->client_email }}</dd>
                    </div>
                </dl>
                @if($website->description)
                    <div class="mt-6">
                        <dt class="text-sm text-gray-500">Description</dt>
                        <dd class="mt-1 text-gray-900">{{ $website->description }}</dd>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded shadow border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payments</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($website->payments as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($payment->payment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->payment_method }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-semibold">${{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">No payments recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


