<x-guest-layout>
    <div class="min-h-screen flex bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Left Side - Branding Section -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-12">
            <div class="max-w-lg">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-3 mb-8">
                    <div class="h-16 w-16 bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-gray-900">SiteManager</span>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    Start Managing Your<br/>
                    <span class="text-green-600">Digital Portfolio Today</span>
                </h1>
                <p class="text-lg text-gray-600 mb-10">
                    Create your free account and get instant access to powerful website management tools.
                </p>

                <!-- Benefits -->
                <div class="space-y-5">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Quick Setup</p>
                            <p class="text-sm text-gray-600">Get started in less than 2 minutes</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Secure & Private</p>
                            <p class="text-sm text-gray-600">Your data is encrypted and protected</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">No Credit Card Required</p>
                            <p class="text-sm text-gray-600">Start with our free plan</p>
                        </div>
                    </div>
                </div>

                <!-- Trust Badge -->
                <div class="mt-10 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
                    <div class="flex items-center space-x-3">
                        <div class="flex -space-x-2">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 border-2 border-white"></div>
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 border-2 border-white"></div>
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-pink-500 to-pink-600 border-2 border-white"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Join 1,000+ users</p>
                            <p class="text-xs text-gray-600">Managing websites with confidence</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center space-x-3">
                        <div class="h-12 w-12 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">SiteManager</span>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Create Your Account</h2>
                        <p class="text-gray-600">Join thousands of website managers</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" value="Full Name" class="block text-sm font-medium text-gray-700 mb-2" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <x-text-input id="name" 
                                            type="text" 
                                            name="name" 
                                            :value="old('name')" 
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                            placeholder="John Doe"
                                            required 
                                            autofocus 
                                            autocomplete="name" />
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

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
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                            placeholder="you@example.com"
                                            required 
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
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                            placeholder="Minimum 8 characters"
                                            required 
                                            autocomplete="new-password" />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" value="Confirm Password" class="block text-sm font-medium text-gray-700 mb-2" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <x-text-input id="password_confirmation" 
                                            type="password" 
                                            name="password_confirmation" 
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                            placeholder="Re-enter password"
                                            required 
                                            autocomplete="new-password" />
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Terms -->
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-xs text-gray-600">
                                By creating an account, you agree to our 
                                <a href="#" class="text-green-600 hover:text-green-700 font-semibold">Terms of Service</a> 
                                and 
                                <a href="#" class="text-green-600 hover:text-green-700 font-semibold">Privacy Policy</a>.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-4 rounded-lg font-medium hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all">
                            Create Account
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-8 mb-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Already have an account?</span>
                            </div>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <a href="{{ route('login') }}" 
                       class="block w-full text-center py-3 px-4 border-2 border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 hover:border-blue-500 hover:text-blue-600 transition-all">
                        Sign In
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
