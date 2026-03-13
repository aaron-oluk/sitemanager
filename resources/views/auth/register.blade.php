@extends('layouts.guest')

@section('title', 'Register - SiteManager')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Join SiteManager</h1>
                <p class="text-gray-600 text-sm">Create your account and start managing</p>
            </div>

            <!-- Register Card -->
            <div class="bg-white rounded-md shadow-sm p-8 border border-gray-100">
                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded focus:bg-white transition-all outline-none text-gray-900"
                               placeholder="John Doe"
                               required 
                               autofocus 
                               autocomplete="name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded focus:bg-white transition-all outline-none text-gray-900"
                               placeholder="you@example.com"
                               required 
                               autocomplete="username">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input id="password" 
                               type="password"
                               name="password"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded focus:bg-white transition-all outline-none text-gray-900"
                               placeholder="Minimum 8 characters"
                               required 
                               autocomplete="new-password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                        <input id="password_confirmation" 
                               type="password" 
                               name="password_confirmation"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded focus:bg-white transition-all outline-none text-gray-900" 
                               placeholder="Repeat your password"
                               required 
                               autocomplete="new-password">
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms Notice -->
                    <div class="bg-gray-50 rounded p-4 border border-gray-200">
                        <p class="text-xs text-gray-600 leading-relaxed">
                            By creating an account, you agree to our 
                            <a href="#" class="text-green-600 hover:text-green-700 font-semibold">Terms</a> 
                            and 
                            <a href="#" class="text-green-600 hover:text-green-700 font-semibold">Privacy Policy</a>.
                        </p>
                    </div>

                    <!-- Create Account Button -->
                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition-all duration-200 shadow-sm hover:shadow-sm">
                        Create Account
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-3 text-gray-500 font-medium">Already have an account?</span>
                    </div>
                </div>

                <!-- Sign In Link -->
                <a href="{{ route('login') }}" 
                   class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-2 px-4 rounded border-2 border-gray-200 hover:border-gray-300 transition-all">
                    Sign In
                </a>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-8">
                &copy; {{ date('Y') }} SiteManager. All rights reserved.
            </p>
        </div>
    </div>
@endsection
