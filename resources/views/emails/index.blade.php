<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Emails') }}</h2>
            <a href="{{ route('emails.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Add Email</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($emails as $email)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $email->email_address }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $email->provider }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $email->hosting_plan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 font-semibold">${{ number_format($email->monthly_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $email->status === 'active' ? 'bg-green-100 text-green-700' : ($email->status === 'suspended' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($email->status) }}</span>
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
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No emails found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $emails->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>


