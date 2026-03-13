@extends('layouts.app')

@section('content')
    <header class="bg-white/80 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Domain') }}</h2>
        </div>
    </header>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">
                    <ul class="list-disc ms-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('domains.update', $domain) }}" class="bg-white rounded shadow border border-gray-100 p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domain Name</label>
                        <input name="domain_name" value="{{ old('domain_name', $domain->domain_name) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registrar</label>
                        <input name="registrar" value="{{ old('registrar', $domain->registrar) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Registration Date</label>
                        <input type="date" name="registration_date" value="{{ old('registration_date', optional($domain->registration_date)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', optional($domain->expiry_date)->format('Y-m-d')) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Annual Cost</label>
                        <input type="number" step="0.01" name="annual_cost" value="{{ old('annual_cost', $domain->annual_cost) }}" required class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500">
                            @foreach(['active','expired','pending'] as $status)
                                <option value="{{ $status }}" {{ old('status', $domain->status)===$status?'selected':'' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-sm border-gray-300 focus:ring-2 focus:ring-purple-500">{{ old('notes', $domain->notes) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('domains.index') }}" class="px-4 py-2 rounded border">Cancel</a>
                    <button class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection


