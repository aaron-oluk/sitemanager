<x-guest-layout>
    <div class="min-h-screen flex bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Left Side - Branding Section -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12">
            <div class="max-w-lg">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-3 mb-8">
                    <div class="h-16 w-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-gray-900">SiteManager</span>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    Manage All Your<br/>
                    <span class="text-blue-600">Websites in One Place</span>
                </h1>
                <p class="text-lg text-gray-600 mb-10">
                    Track domains, payments, and emails effortlessly with our comprehensive website management platform.
                </p>

                <!-- Features -->
                <div class="space-y-5">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Centralized Dashboard</p>
                            <p class="text-sm text-gray-600">Manage all websites from one interface</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Payment Tracking</p>
                            <p class="text-sm text-gray-600">Never miss a billing cycle or renewal</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Domain Management</p>
                            <p class="text-sm text-gray-600">Track all domains and DNS settings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center space-x-3">
                        <div class="h-12 w-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">SiteManager</span>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                        <p class="text-gray-600">Sign in to access your account</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-6" :status="session('status')" />

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" value="Email Address" class="block text-sm font-medium text-gray-700 mb-2" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <x-text-input id="email" 
                                            type="email" 
                                            name="email" 
                                            :value="old('email')" 
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            placeholder="you@example.com"
                                            required 
                                            autofocus 
                                            autocomplete="username" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" value="Password" class="block text-sm font-medium text-gray-700 mb-2" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <x-text-input id="password" 
                                            type="password"
                                            name="password"
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            placeholder="Enter your password"
                                            required 
                                            autocomplete="current-password" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center cursor-pointer">
                                <input id="remember_me" 
                                       type="checkbox" 
                                       name="remember"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Remember me</span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            Sign In
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-8 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Don't have an account?</span>
                            </div>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <a href="{{ route('register') }}" 
                       class="block w-full text-center py-3 px-4 border-2 border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 hover:border-blue-500 hover:text-blue-600 transition-all">
                        Create Account
                    </a>
                </div>

                <!-- Footer -->
                <p class="mt-8 text-center text-xs text-gray-500">
                    © {{ date('Y') }} SiteManager. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
