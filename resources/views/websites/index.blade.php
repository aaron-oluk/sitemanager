@extends('layouts.app')

@section('content')
<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Websites</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $totalWebsites }} total · {{ $activeWebsites }} active · ${{ number_format($totalRevenue, 0) }} revenue</p>
        </div>
        <a href="{{ route('websites.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Website
        </a>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg w-fit">
        @php
            $tabs = [
                null         => 'All (' . $totalWebsites . ')',
                'active'     => 'Active (' . $activeWebsites . ')',
                'maintenance'=> 'Maintenance (' . $maintenanceWebsites . ')',
                'inactive'   => 'Inactive (' . $inactiveWebsites . ')',
            ];
        @endphp
        @foreach($tabs as $tabValue => $tabLabel)
        <a href="{{ route('websites.index', $tabValue ? ['status' => $tabValue] : []) }}"
           class="px-3.5 py-1.5 rounded-md text-sm font-medium transition-colors
               {{ $status === $tabValue ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            {{ $tabLabel }}
        </a>
        @endforeach
    </div>

    {{-- Table --}}
    @if($websites->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
            <p class="text-gray-500 text-sm mb-3">{{ $status ? 'No ' . $status . ' websites.' : 'No websites yet.' }}</p>
            <a href="{{ route('websites.create') }}" class="text-indigo-600 hover:underline text-sm font-medium">Add your first website →</a>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Host</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deployed</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($websites as $website)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-900">{{ $website->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $website->domain_name }}</p>
                            @if($website->domainRelation && $website->domainRelation->isExpiringSoon())
                                <span class="inline-flex items-center gap-1 text-xs text-amber-600 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Domain expires {{ optional($website->domainRelation->expiry_date)->format('M j') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm text-gray-900">{{ $website->client_name }}</p>
                            <a href="mailto:{{ $website->client_email }}" class="text-xs text-gray-400 hover:text-indigo-600 transition-colors">{{ $website->client_email }}</a>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $website->host_server }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ optional($website->deployment_date)->format('M j, Y') }}</td>
                        <td class="px-5 py-3.5 text-sm font-semibold text-gray-900 text-right">{{ $website->formatted_amount }}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $website->status === 'active' ? 'bg-green-100 text-green-700' :
                                   ($website->status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($website->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3 text-sm">
                                <a href="{{ route('websites.show', $website) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                <a href="{{ route('websites.edit', $website) }}" class="text-gray-500 hover:text-gray-700">Edit</a>
                                <form action="{{ route('websites.destroy', $website) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($website->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-3 border-t border-gray-100">{{ $websites->links() }}</div>
        </div>
    @endif

</div>
@endsection
