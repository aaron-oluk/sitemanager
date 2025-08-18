<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Websites') }}</h2>
            <a href="{{ route('websites.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Add Website</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                    <p class="text-sm text-gray-600">Total Websites</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalWebsites }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow border border-gray-100 p-4">
                    <p class="text-sm text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeWebsites }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deployment</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($websites as $website)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $website->name }}</td>
                                    <td class="px-4 py-3 text-sm text-blue-700">{{ $website->domain_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $website->host_server }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ optional($website->deployment_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ $website->formatted_amount }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $website->status === 'active' ? 'bg-green-100 text-green-700' : ($website->status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($website->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('websites.show', $website) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        <a href="{{ route('websites.edit', $website) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form action="{{ route('websites.destroy', $website) }}" method="POST" class="inline" onsubmit="return confirm('Delete this website?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">No websites yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $websites->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>


