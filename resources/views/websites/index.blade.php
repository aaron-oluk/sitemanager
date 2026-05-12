@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Websites</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $totalWebsites }} total · {{ $activeWebsites }} active</p>
        </div>
        <a href="{{ route('websites.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Website
        </a>
    </div>

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalWebsites }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $activeWebsites }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Active</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 text-center">
            <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 0) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Revenue (USD)</p>
        </div>
    </div>

    {{-- Website cards --}}
    @if($websites->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/></svg>
            <p class="text-gray-500 text-sm mb-3">No websites yet.</p>
            <a href="{{ route('websites.create') }}" class="text-indigo-600 hover:underline text-sm font-medium">Add your first website →</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($websites as $website)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col hover:shadow-md transition-shadow">
                {{-- Card header --}}
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $website->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $website->domain_name }}</p>
                        </div>
                        <span class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $website->status === 'active' ? 'bg-green-100 text-green-700' :
                               ($website->status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($website->status) }}
                        </span>
                    </div>
                </div>

                {{-- Card body --}}
                <div class="px-5 py-4 flex-1 space-y-2.5">
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-gray-700 truncate">{{ $website->client_name }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                        <span class="text-gray-700 truncate">{{ $website->host_server }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-gray-500">Deployed {{ optional($website->deployment_date)->format('M j, Y') }}</span>
                    </div>
                    @if($website->domainRelation && $website->domainRelation->isExpiringSoon())
                    <div class="flex items-center gap-2 text-xs bg-amber-50 text-amber-700 px-2.5 py-1.5 rounded-lg">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Domain expires {{ optional($website->domainRelation->expiry_date)->format('M j') }}
                    </div>
                    @endif
                </div>

                {{-- Card footer --}}
                <div class="px-5 py-3 bg-gray-50 rounded-b-xl flex items-center justify-between border-t border-gray-100">
                    <span class="text-sm font-bold text-gray-900">{{ $website->formatted_amount }}</span>
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('websites.show', $website) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                        <a href="{{ route('websites.edit', $website) }}" class="text-gray-500 hover:text-gray-700">Edit</a>
                        <form action="{{ route('websites.destroy', $website) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($website->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div>{{ $websites->links() }}</div>
    @endif

</div>
@endsection
