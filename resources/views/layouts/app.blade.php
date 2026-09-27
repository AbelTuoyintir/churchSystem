<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Church Management System') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    <div class="min-h-screen flex">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col shrink-0 min-h-screen">
            <div class="p-4 text-xl font-bold border-b border-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4" />
                </svg>
                <span>ChMS</span>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('people.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('people.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 001 1m-6 0h6" />
                    </svg>
                    People
                </a>

                <a href="{{ route('households.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('households.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Households
                </a>
            </nav>

            @auth
            <div class="p-4 border-t border-gray-800 text-xs text-gray-400">
                <p class="font-semibold text-gray-200">{{ auth()->user()->name }}</p>
                <p class="capitalize">Role: {{ auth()->user()->role }}</p>
            </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-800">
                    {{ $header ?? 'Church Management' }}
                </h1>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-6">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
