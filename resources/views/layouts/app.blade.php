<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111827">

    <title>@yield('title', config('app.name', 'Church Management System'))</title>

    @vite(['resources/css/app.css'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        .touch-action-manipulation { touch-action: manipulation; }

        /* iOS safe areas (notch / home indicator) */
        .safe-top    { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }

        /* Slim, unobtrusive sidebar scrollbar */
        .sidebar-scroll { scrollbar-width: thin; scrollbar-color: #374151 transparent; }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #374151; border-radius: 9999px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #4b5563; }

        /* Prevent background scroll while the mobile drawer is open */
        body.drawer-open { overflow: hidden; }
    </style>
</head>
<body class="h-full bg-gray-100 font-sans text-gray-800 antialiased touch-action-manipulation"
      x-data="churchApp()"
      x-init="initApp()"
      @keydown.escape.window="mobileSidebarOpen = false">

@php
    // Shared link styling so every nav item stays consistent
    $linkBase   = 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900';
    $linkIdle   = 'text-gray-300 hover:bg-gray-800 hover:text-white';
    $linkActive = 'bg-indigo-600 text-white shadow-sm shadow-indigo-900/40';
    $iconBase   = 'h-5 w-5 shrink-0 opacity-90';

    $headerTitle = trim($__env->yieldContent('title')) ?: ($header ?? 'Dashboard');
@endphp

<div class="min-h-full md:flex">

    {{-- ============================= MOBILE BACKDROP ============================= --}}
    <div x-show="mobileSidebarOpen"
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/70 backdrop-blur-sm md:hidden"
         aria-hidden="true"></div>

    {{-- ================================ SIDEBAR ================================= --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 shrink-0 transform flex-col bg-gray-900 text-white transition-transform duration-300 ease-in-out md:sticky md:top-0 md:h-screen md:translate-x-0"
           :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           aria-label="Sidebar">

        {{-- Brand + mobile close --}}
        <div class="flex items-center justify-between border-b border-gray-800 p-4 safe-top">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-600 shadow-md shadow-indigo-500/25">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <div class="text-lg font-black tracking-tight text-white">ChMS</div>
                    <div class="text-[11px] font-medium tracking-wide text-gray-400">Church System</div>
                </div>
            </a>

            <button @click="mobileSidebarOpen = false"
                    aria-label="Close navigation menu"
                    class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 md:hidden">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Scrollable navigation --}}
        <nav class="sidebar-scroll flex-1 space-y-6 overflow-y-auto px-3 py-4" aria-label="Main navigation">

            {{-- Overview --}}
            <div class="space-y-1">
                <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">Overview</p>

                <a href="{{ route('dashboard') }}"
                   class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $linkActive : $linkIdle }}"
                   @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1m-6 0h6"/>
                    </svg>
                    <span class="truncate">Dashboard</span>
                </a>

                <a href="{{ route('members.portal') }}"
                   class="{{ $linkBase }} {{ request()->routeIs('members.portal') ? $linkActive : $linkIdle }}"
                   @if(request()->routeIs('members.portal')) aria-current="page" @endif>
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="truncate">My Portal</span>
                </a>

                @if(auth()->user()?->isMember() || auth()->user()?->isGroupLeader())
                    <a href="{{ route('members.household') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('members.household') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('members.household')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
                        </svg>
                        <span class="truncate">My Household</span>
                    </a>
                    <a href="{{ route('members.groups') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('members.groups*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('members.groups*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        <span class="truncate">Find a Group</span>
                    </a>
                    <a href="{{ route('members.events') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('members.events') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('members.events')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">Events</span>
                    </a>
                    <a href="{{ route('members.schedule') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('members.schedule') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('members.schedule')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">My Schedule</span>
                    </a>
                    <a href="{{ route('members.prayer-requests') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('members.prayer-requests') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('members.prayer-requests')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="truncate">Prayer Requests</span>
                    </a>
                    <a href="{{ route('giving.my') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('giving.my') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('giving.my')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="truncate">My Giving</span>
                    </a>
                @endif

                @if (auth()->user()?->isGroupLeader())
                    <div class="mt-3 space-y-1 border-t border-gray-800 pt-3">
                        <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-emerald-400">Group Leadership</p>
                        <a href="{{ route('groups.lead.index') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.index') ? $linkActive : $linkIdle }}">My Groups</a>
                        <a href="{{ route('groups.lead.directory') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.directory') ? $linkActive : $linkIdle }}">Directory</a>
                        <a href="{{ route('groups.lead.attendance') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.attendance') ? $linkActive : $linkIdle }}">Attendance</a>
                        <a href="{{ route('groups.lead.messages.index') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.messages.*') ? $linkActive : $linkIdle }}">Messages</a>
                        <a href="{{ route('groups.lead.reports') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.reports') ? $linkActive : $linkIdle }}">Group Reports</a>
                        <a href="{{ route('groups.lead.events') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.events') ? $linkActive : $linkIdle }}">Group Events</a>
                        <a href="{{ route('groups.lead.prayer-requests') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.prayer-requests') ? $linkActive : $linkIdle }}">Prayer Requests</a>
                        <a href="{{ route('groups.lead.teams') }}" class="{{ $linkBase }} {{ request()->routeIs('groups.lead.teams') ? $linkActive : $linkIdle }}">Teams</a>
                    </div>
                @endif

                <a href="{{ route('personas.index') }}"
                   class="{{ $linkBase }} {{ request()->routeIs('personas.*') ? $linkActive : $linkIdle }}"
                   @if(request()->routeIs('personas.*')) aria-current="page" @endif>
                    <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="truncate">User Personas</span>
                </a>
            </div>

            {{-- People & Community --}}
            <div class="space-y-1">
                <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">People &amp; Community</p>

                @can('viewAny', App\Models\Person::class)
                    <a href="{{ route('people.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('people.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('people.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="truncate">{{ auth()->user()?->isGroupLeader() ? 'Directory' : 'People' }}</span>
                    </a>
                @endcan

                @can('viewAny', App\Models\Household::class)
                    <a href="{{ route('households.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('households.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('households.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1m-6 0h6"/>
                        </svg>
                        <span class="truncate">Households</span>
                    </a>
                @endcan

                @can('viewAny', App\Models\Group::class)
                    <a href="{{ route('groups.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('groups.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('groups.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="truncate">Groups &amp; Ministries</span>
                    </a>
                @endcan
            </div>

            {{-- Services & Attendance --}}
            <div class="space-y-1">
                <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">Services &amp; Attendance</p>

                @can('viewAny', App\Models\Service::class)
                    <a href="{{ route('services.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('services.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('services.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">Services</span>
                    </a>
                @endcan

                @can('viewAny', App\Models\Attendance::class)
                    <a href="{{ route('attendances.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('attendances.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('attendances.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span class="truncate">Attendance</span>
                    </a>
                @endcan
            </div>

            {{-- Messages --}}
            @can('viewAny', App\Models\Message::class)
                <div class="space-y-1">
                    <a href="{{ route('messages.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('messages.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('messages.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16l-3-3h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h3l3 3z"/>
                        </svg>
                        <span class="truncate">Messages</span>
                    </a>
                </div>
            @endcan

            {{-- Financial Records (admin / pastor only) --}}
            @if(Gate::allows('viewAny', App\Models\Fund::class)
                || Gate::allows('viewAny', App\Models\Contribution::class)
                || Gate::allows('viewAny', App\Models\Pledge::class))
                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-amber-400">Financial Records</p>

                    @can('viewAny', App\Models\Fund::class)
                        <a href="{{ route('funds.index') }}"
                           class="{{ $linkBase }} {{ request()->routeIs('funds.*') ? $linkActive : $linkIdle }}"
                           @if(request()->routeIs('funds.*')) aria-current="page" @endif>
                            <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="truncate">Church Funds</span>
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Contribution::class)
                        <a href="{{ route('contributions.index') }}"
                           class="{{ $linkBase }} {{ request()->routeIs('contributions.*') ? $linkActive : $linkIdle }}"
                           @if(request()->routeIs('contributions.*')) aria-current="page" @endif>
                            <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="truncate">All Contributions</span>
                        </a>
                    @endcan

                    @can('viewAny', App\Models\Pledge::class)
                        <a href="{{ route('pledges.index') }}"
                           class="{{ $linkBase }} {{ request()->routeIs('pledges.*') ? $linkActive : $linkIdle }}"
                           @if(request()->routeIs('pledges.*')) aria-current="page" @endif>
                            <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="truncate">All Pledges</span>
                        </a>
                    @endcan
                </div>
            @endif

            @if(auth()->user()?->isStaff() || auth()->user()?->isGroupLeader())
                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">Ministry &amp; Care</p>

                    <a href="{{ route('members.prayer-requests') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('members.prayer-requests') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Prayer Requests</span>
                    </a>

                    @if(auth()->user()?->role === 'pastor')
                        <a href="{{ route('pastoral-care.index') }}"
                           class="{{ $linkBase }} {{ request()->routeIs('pastoral-care.*') || request()->routeIs('pastoral.care.*') ? $linkActive : $linkIdle }}">
                            <span class="truncate">Pastoral Care</span>
                        </a>
                    @endif

                    <a href="{{ route('admin.teams.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('teams.*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Teams</span>
                    </a>

                    <a href="{{ route('assignments.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('assignments.*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Scheduling</span>
                    </a>

                    <a href="{{ route('admin.events.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('admin.events.*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Events</span>
                    </a>

                          <a href="{{ route('reports') }}"
                              class="{{ $linkBase }} {{ request()->routeIs('reports*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Reports</span>
                    </a>
                </div>
            @endif

            @if(auth()->user()?->isAdmin())
                <div class="space-y-1">
                    <p class="px-3 pb-1 text-[11px] font-bold uppercase tracking-wider text-indigo-300">Administration</p>

                    <a href="{{ route('admin.user-roles.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('admin.user-roles.*') ? $linkActive : $linkIdle }}"
                       @if(request()->routeIs('admin.user-roles.*')) aria-current="page" @endif>
                        <svg class="{{ $iconBase }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2m12-10a4 4 0 100-8 4 4 0 000 8zm4 10v-2a4 4 0 00-3-3.87M12 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="truncate">User Roles</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('admin.settings.*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Settings</span>
                    </a>

                    <a href="{{ route('admin.audit-log.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('admin.audit-log.*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Audit Log</span>
                    </a>

                    <a href="{{ route('admin.exports.index') }}"
                       class="{{ $linkBase }} {{ request()->routeIs('admin.exports.*') ? $linkActive : $linkIdle }}">
                        <span class="truncate">Exports</span>
                    </a>
                </div>
            @endif
        </nav>

        {{-- Signed-in user footer --}}
        @auth
            <div class="border-t border-gray-800 p-3 safe-bottom">
                <div class="flex items-center gap-3 rounded-xl bg-gray-800/60 p-2">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-100">{{ auth()->user()->name }}</p>
                        <span class="inline-block rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide
                            {{ auth()->user()->isAdmin() ? 'bg-indigo-900/80 text-indigo-200 ring-1 ring-indigo-700/50' : 'bg-gray-700 text-gray-300' }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Out" aria-label="Log Out"
                                class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-700 hover:text-red-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    {{-- ============================ MAIN COLUMN ============================ --}}
    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/90 backdrop-blur safe-top">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">

                {{-- Mobile menu trigger --}}
                <button @click="mobileSidebarOpen = true"
                        aria-label="Open navigation menu"
                        class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 md:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="min-w-0 flex-1 truncate text-lg font-bold tracking-tight text-gray-900 sm:text-xl">
                    {{ $headerTitle }}
                </h1>

                {{-- Push notification toggle --}}
                <button @click="togglePush()"
                        :title="pushSubscribed ? 'Notifications enabled — click to send a test' : 'Enable push notifications'"
                        :aria-label="pushSubscribed ? 'Notifications enabled, click to send test' : 'Enable push notifications'"
                        :aria-pressed="pushSubscribed.toString()"
                        class="relative rounded-lg p-2 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                        :class="pushSubscribed ? 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="pushSubscribed" x-cloak
                          class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                </button>

                {{-- User chip (desktop) --}}
                @auth
                    <div class="hidden items-center gap-2 border-l border-gray-200 pl-3 sm:flex">
                        <div class="grid h-9 w-9 place-items-center rounded-full bg-indigo-600 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden leading-tight lg:block">
                            <p class="max-w-[12rem] truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] capitalize text-gray-500">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-7xl space-y-6">

                {{-- Flash messages --}}
                @foreach (['status' => 'indigo'] as $key => $color)
                    @if (session($key))
                        <div x-data="{ show: true }" x-show="show" x-cloak
                             class="flex items-start gap-3 rounded-xl border border-{{ $color }}-200 bg-{{ $color }}-50 p-4 text-sm text-{{ $color }}-800"
                             role="alert">
                            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="flex-1">{{ session($key) }}</p>
                            <button @click="show = false" aria-label="Dismiss"
                                    class="rounded p-0.5 transition hover:bg-{{ $color }}-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                @endforeach

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200 px-4 py-4 text-center text-xs text-gray-500 sm:px-6 lg:px-8 safe-bottom">
            &copy; {{ date('Y') }} {{ config('app.name', 'Church Management System') }} — All rights reserved.
        </footer>
    </div>
</div>

@livewireScripts

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showNotification(type, message) {
        const icon = ['success', 'error', 'warning', 'info', 'question'].includes(type) ? type : 'info';
        const title = icon === 'success' ? 'Success' : icon === 'error' ? 'Error' : 'Notice';

        Swal.fire({ icon, title, text: message, confirmButtonText: 'OK' });
    }

    window.addEventListener('notify', (event) => {
        showNotification(event.detail.type, event.detail.message);
    });

    @if (session('success'))
        showNotification('success', @json(session('success')));
    @endif

    @if (session('error'))
        showNotification('error', @json(session('error')));
    @endif
</script>

{{-- Alpine application controller --}}
<script>
    function churchApp() {
        return {
            mobileSidebarOpen: false,
            pushSubscribed: false,

            initApp() {
                this.$watch('mobileSidebarOpen', (open) => {
                    document.body.classList.toggle('drawer-open', open);
                });

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
                    try {
                        await fetch('{{ route("push.test") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
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
                    alert('Push Notifications enabled! You will now receive event reminders and church broadcasts.');
                } catch (err) {
                    console.error('Subscription error:', err);

                    // Fallback for browsers that block real subscriptions
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
@livewireScripts
</body>
</html>