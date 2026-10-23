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
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('members.portal') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('members.portal') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    My Portal
                </a>

                @can('viewAny', App\Models\Person::class)
                <a href="{{ route('people.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('people.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 001 1m-6 0h6" />
                    </svg>
                    People
                </a>
                @endcan

                @can('viewAny', App\Models\Household::class)
                <a href="{{ route('households.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('households.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Households
                </a>
                @endcan

                @can('viewAny', App\Models\Group::class)
                <a href="{{ route('groups.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('groups.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Groups
                </a>
                @endcan

                @can('viewAny', App\Models\Service::class)
                <a href="{{ route('services.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('services.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Services
                </a>
                @endcan

                @can('viewAny', App\Models\Attendance::class)
                <a href="{{ route('attendances.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('attendances.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Attendance
                </a>
                @endcan

                @can('viewAny', App\Models\Message::class)
                <a href="{{ route('messages.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('messages.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Messages
                </a>
                @endcan

                @can('viewAny', App\Models\Fund::class)
                <a href="{{ route('funds.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('funds.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Funds
                </a>
                @endcan

                @can('viewAny', App\Models\Contribution::class)
                <a href="{{ route('contributions.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('contributions.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Contributions
                </a>
                @endcan

                @can('viewAny', App\Models\Pledge::class)
                <a href="{{ route('pledges.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('pledges.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pledges
                </a>
                @endcan
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
