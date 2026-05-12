@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Domains</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track registrations and expiry dates</p>
        </div>
        <a href="{{ route('domains.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-sm font-medium text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Register Domain
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Domain</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Expiry</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Annual Cost</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($domains as $domain)
                @php
                    $daysLeft = $domain->expiry_date ? now()->diffInDays($domain->expiry_date, false) : null;
                    $urgent   = $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 7;
                    $warning  = $daysLeft !== null && $daysLeft > 7 && $daysLeft <= 30;
                    $expired  = $daysLeft !== null && $daysLeft < 0;
                @endphp
                <tr class="{{ $urgent ? 'bg-red-50' : ($warning ? 'bg-amber-50' : '') }} hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            @if($urgent)
                                <span class="h-2 w-2 rounded-full bg-red-500 shrink-0"></span>
                            @elseif($warning)
                                <span class="h-2 w-2 rounded-full bg-amber-400 shrink-0"></span>
                            @else
                                <span class="h-2 w-2 rounded-full bg-green-400 shrink-0"></span>
                            @endif
                            <a href="{{ route('domains.show', $domain) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                {{ $domain->domain_name }}
                            </a>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $domain->registrar }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ optional($domain->registration_date)->format('M j, Y') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="text-sm {{ $urgent ? 'text-red-700 font-semibold' : ($warning ? 'text-amber-700 font-medium' : ($expired ? 'text-gray-400 line-through' : 'text-gray-900')) }}">
                            {{ optional($domain->expiry_date)->format('M j, Y') }}
                        </div>
                        @if($expired)
                            <div class="text-xs text-red-600 font-medium">Expired {{ abs((int)$daysLeft) }}d ago</div>
                        @elseif($urgent)
                            <div class="text-xs text-red-600 font-medium">{{ (int)$daysLeft }}d left — urgent</div>
                        @elseif($warning)
                            <div class="text-xs text-amber-600">{{ (int)$daysLeft }}d left</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-sm font-semibold text-gray-900">${{ number_format($domain->annual_cost, 2) }}</td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $domain->status === 'active' ? 'bg-green-100 text-green-700' :
                               ($domain->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($domain->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 text-sm">
                            <a href="{{ route('domains.show', $domain) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                            <a href="{{ route('domains.edit', $domain) }}" class="text-gray-500 hover:text-gray-700">Edit</a>
                            <form action="{{ route('domains.destroy', $domain) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($domain->domain_name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500">
                        No domains registered yet.
                        <a href="{{ route('domains.create') }}" class="text-indigo-600 hover:underline ml-1">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">{{ $domains->links() }}</div>
    </div>

</div>
@endsection
