@extends('layouts.guest')

@section('title', 'Forgot Password - SiteManager')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Forgot Password</h1>
                <p class="text-gray-600 text-sm">We'll email you a reset link</p>
            </div>

            <div class="bg-white rounded-md shadow-sm p-8 border border-gray-100">
                <p class="mb-6 text-sm text-gray-600">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}</p>

                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded focus:bg-white transition-all outline-none text-gray-900"
                               placeholder="you@example.com" required autofocus>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-colors">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
