@extends('layouts.guest')

@section('title', 'Login - SiteManager')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome to SiteManager</h1>
                <p class="text-gray-600 text-sm">Sign in to manage your websites</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded shadow-sm p-8 border border-gray-100">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded focus:bg-white transition-all outline-none text-gray-900"
                               placeholder="Enter your email"
                               required 
                               autofocus 
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
                               placeholder="Enter your password"
                               required 
                               autocomplete="current-password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" 
                                   name="remember"
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                            <span class="ml-2.5 text-sm text-gray-600 group-hover:text-gray-900">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded transition-all duration-200 shadow-sm hover:shadow-sm">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-3 text-gray-500 font-medium">New to SiteManager?</span>
                    </div>
                </div>

                <!-- Sign Up Link -->
                <a href="{{ route('register') }}" 
                   class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-2.5 px-4 rounded border-2 border-gray-200 hover:border-gray-300 transition-all">
                    Create an Account
                </a>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-8">
                &copy; {{ date('Y') }} SiteManager. All rights reserved.
            </p>
        </div>
    </div>
@endsection
