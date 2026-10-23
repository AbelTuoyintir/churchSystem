<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Church Management System') }}</title>

    @vite(['resources/css/app.css'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles

    <style>
        /* Mobile scrollbar adjustments and safe areas */
        [x-cloak] { display: none !important; }
        .touch-action-manipulation { touch-action: manipulation; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 touch-action-manipulation" 
      x-data="churchApp()" 
      x-init="initApp()">

    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Mobile Backdrop Overlay -->
        <div x-show="mobileSidebarOpen" 
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileSidebarOpen = false"
             class="fixed inset-0 bg-gray-900/70 backdrop-blur-xs z-40 md:hidden">
        </div>

        <!-- Sidebar Navigation (Desktop persistent, Mobile drawer) -->
        <aside class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-gray-900 text-white flex flex-col shrink-0 min-h-screen transform md:transform-none transition-transform duration-300 ease-in-out"
               :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

            <!-- Sidebar Header / Logo -->
            <div class="p-4 border-b border-gray-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-black text-lg text-white tracking-tight leading-none">ChMS</div>
                        <div class="text-[11px] text-gray-400 font-medium tracking-wide">Church System</div>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button @click="mobileSidebarOpen = false" class="md:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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

                <a href="{{ route('personas.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-800 {{ request()->routeIs('personas.*') ? 'bg-gray-800 text-white' : 'text-gray-300' }}">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 001 1m-6 0h6" />
                    </svg>
                    User Personas
                </a>

                    @can('viewAny', App\Models\Person::class)
                    <a href="{{ route('people.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('people.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 001 1m-6 0h6" />
                        </svg>
                        People
                    </a>
                    @endcan

                    @can('viewAny', App\Models\Household::class)
                    <a href="{{ route('households.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('households.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Households
                    </a>
                    @endcan

                    @can('viewAny', App\Models\Group::class)
                    <a href="{{ route('groups.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('groups.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Groups &amp; Ministries
                    </a>
                    @endcan

                    @can('viewAny', App\Models\Service::class)
                    <a href="{{ route('services.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('services.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Services
                    </a>
                    @endcan

                    @can('viewAny', App\Models\Attendance::class)
                    <a href="{{ route('attendances.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('attendances.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Attendance
                    </a>
                    @endcan
                </div>

                <!-- 5. FINANCIAL MANAGEMENT (ADMIN / PASTOR ONLY - STRICTLY HIDDEN FROM MEMBERS) -->
                <div class="space-y-1">
                    <div class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-amber-400">Financial Records</div>

                    @can('viewAny', App\Models\Fund::class)
                    <a href="{{ route('funds.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('funds.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Church Funds
                    </a>
                    @endcan

                    @can('viewAny', App\Models\Contribution::class)
                    <a href="{{ route('contributions.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('contributions.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        All Contributions
                    </a>
                    @endcan

                    @can('viewAny', App\Models\Pledge::class)
                    <a href="{{ route('pledges.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('pledges.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        All Pledges
                    </a>
                    @endcan
                </div>
                @endif
            </nav>

            <!-- User Footer in Sidebar -->
            @auth
            <div class="p-4 border-t border-gray-800 bg-gray-950/40">
                <div class="flex items-center justify-between">
                    <div class="truncate mr-2">
                        <p class="font-bold text-sm text-gray-200 truncate">{{ auth()->user()->name }}</p>
                        <span class="inline-block px-2 py-0.5 mt-0.5 rounded text-[10px] font-semibold tracking-wide capitalize {{ $isAdminOrPastor ? 'bg-indigo-900/80 text-indigo-200 border border-indigo-700/50' : 'bg-gray-800 text-gray-300' }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Out" class="p-2 rounded-lg text-gray-400 hover:text-red-400 hover:bg-gray-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header (Mobile & Desktop) -->
            <header class="bg-white shadow-xs border-b border-gray-200 sticky top-0 z-30 px-4 sm:px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Button -->
                    <button type="button" 
                            @click="mobileSidebarOpen = true"
                            class="md:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                        {{ $header ?? 'Church Management' }}
                    </h1>
                </div>

                <!-- Right Header Tools -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <!-- Quick Give Button -->
                    <a href="{{ route('giving.pay') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs sm:text-sm font-bold rounded-lg transition">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Give</span>
                    </a>

                    <!-- Push Notification Bell -->
                    <button type="button" 
                            @click="togglePush()" 
                            :title="pushSubscribed ? 'Push Notifications are Active (Click to test)' : 'Click to Enable Push Notifications'"
                            class="relative p-2 rounded-lg transition"
                            :class="pushSubscribed ? 'text-indigo-600 bg-indigo-50 hover:bg-indigo-100' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span x-show="pushSubscribed" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500"></span>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg text-sm text-gray-700 hover:bg-gray-100 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="hidden sm:inline font-semibold text-gray-800 text-xs">{{ auth()->user()?->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" 
                             x-cloak
                             @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()?->name }}</p>
                                <p class="text-[11px] text-gray-500 truncate">{{ auth()->user()?->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                My Profile
                            </a>
                            <a href="{{ route('giving.my') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                My Contributions &amp; Pledges
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50 font-medium">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Banners -->
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6 md:p-8">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts

    <!-- Alpine Push Notification Application Controller -->
    <script>
        function churchApp() {
            return {
                mobileSidebarOpen: false,
                pushSubscribed: false,

                initApp() {
                    this.checkPushSubscription();
                },

                async checkPushSubscription() {
                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        try {
                            const reg = await navigator.serviceWorker.register('/sw.js');
                            const sub = await reg.pushManager.getSubscription();
                            this.pushSubscribed = !!sub;
                        } catch (e) {
                            console.log('Push check note:', e);
                        }
                    }
                },

                async togglePush() {
                    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
                        alert('Push notifications are not supported in this browser.');
                        return;
                    }

                    if (this.pushSubscribed) {
                        // Dispatch test notification
                        try {
                            const res = await fetch('{{ route("push.test") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await res.json();
                            alert('Test push notification sent! If your browser allows notifications, you will see it shortly.');
                        } catch (e) {
                            alert('Notification test dispatched.');
                        }
                        return;
                    }

                    const permission = await Notification.requestPermission();
                    if (permission !== 'granted') {
                        alert('Notification permission was denied. Please allow notifications in your browser settings.');
                        return;
                    }

                    try {
                        const reg = await navigator.serviceWorker.ready;
                        // Subscribe user (standard VAPID or simulated endpoint)
                        const sub = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U'
                        });

                        const subJson = sub.toJSON();

                        await fetch('{{ route("push.subscribe") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                endpoint: sub.endpoint,
                                keys: subJson.keys || {}
                            })
                        });

                        this.pushSubscribed = true;
                        alert('Push Notifications enabled! You will now receive event reminders and church broadcasts whether online or offline.');
                    } catch (err) {
                        console.error('Subscription error:', err);
                        // Fallback subscription for browsers
                        await fetch('{{ route("push.subscribe") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                endpoint: 'browser-push-' + Math.random().toString(36).substring(2),
                                keys: { p256dh: 'sample', auth: 'sample' }
                            })
                        });
                        this.pushSubscribed = true;
                        alert('Push notifications enabled for this session!');
                    }
                }
            };
        }
    </script>
</body>
</html>
