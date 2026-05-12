<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SiteManager'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('layouts.navigation')

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

    {{-- Main area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden lg:ml-64">

        {{-- Mobile top bar --}}
        <div class="lg:hidden bg-white border-b border-gray-200 px-4 h-14 flex items-center justify-between shrink-0">
            <button onclick="openSidebar()" class="p-1 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold text-gray-900">SiteManager</span>
            <div class="w-7"></div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-6 mt-5 p-3 rounded-lg bg-green-50 text-green-800 border border-green-200 flex items-center justify-between" id="flash-msg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
                <button onclick="document.getElementById('flash-msg').remove()" class="text-green-600 hover:text-green-900 ml-4">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-5 p-3 rounded-lg bg-red-50 text-red-800 border border-red-200 flex items-center justify-between" id="flash-err">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 012 0v1a1 1 0 01-2 0v-1zm0-4a1 1 0 012 0v2a1 1 0 01-2 0V9z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
                <button onclick="document.getElementById('flash-err').remove()" class="text-red-600 hover:text-red-900 ml-4">✕</button>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function openSidebar() {
        document.getElementById('app-sidebar').classList.remove('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.remove('hidden');
    }
    function closeSidebar() {
        document.getElementById('app-sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.add('hidden');
    }
</script>
@yield('scripts')
</body>
</html>
