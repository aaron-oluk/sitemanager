@extends('layouts.app')

@section('header')
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Domains') }}</h2>
            <a href="{{ route('domains.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-medium">Add Domain</a>
        </div>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded shadow border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Annual Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($domains as $domain)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $domain->domain_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $domain->registrar }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($domain->registration_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($domain->expiry_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-semibold">${{ number_format($domain->annual_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $domain->status === 'active' ? 'bg-green-100 text-green-700' : ($domain->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($domain->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('domains.show', $domain) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        <a href="{{ route('domains.edit', $domain) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form action="{{ route('domains.destroy', $domain) }}" method="POST" class="inline" onsubmit="return confirm('Delete this domain?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No domains yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $domains->links() }}</div>
            </div>
        </div>
    </div>
@endsection


