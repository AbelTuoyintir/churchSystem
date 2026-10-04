<div class="max-w-7xl mx-auto space-y-8">
    <!-- Page Banner -->
    <div class="bg-gradient-to-r from-gray-900 via-indigo-900 to-gray-900 text-white p-8 rounded-2xl shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 100 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 001 1m-6 0h6" />
                </svg>
                Church Management System
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight">System User Personas</h1>
            <p class="text-indigo-200 mt-2 text-sm max-w-2xl leading-relaxed">
                Explore and switch between all 4 key user personas in the application. Test permissions, user flows, navigation access, and role-based capabilities in real-time.
            </p>
        </div>

        @auth
            <div class="bg-white/10 backdrop-blur-md px-5 py-4 rounded-xl border border-white/10 text-right shrink-0">
                <span class="text-xs text-indigo-200 block uppercase font-medium">Currently Logged In As</span>
                <span class="text-lg font-bold text-white block">{{ $currentUser->name }}</span>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500 text-white capitalize">
                    Role: {{ $currentUser->role }}
                </span>
            </div>
        @else
            <div class="bg-white/10 backdrop-blur-md px-5 py-4 rounded-xl border border-white/10 text-right shrink-0">
                <span class="text-xs text-indigo-200 block uppercase font-medium">Current Status</span>
                <span class="text-lg font-bold text-white block">Unauthenticated</span>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500 text-white capitalize">
                    Role: Guest
                </span>
            </div>
        @endauth
    </div>

    <!-- Personas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- Persona 1: Admin -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xl shrink-0">
                            🛡️
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Administrator</h2>
                            <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2.5 py-0.5 rounded-full">Role: admin</span>
                        </div>
                    </div>
                    @if($currentUser && $currentUser->role === 'admin')
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">
                    Full administrative control over all ministry operations, directory records, messages, services, and sensitive financial data (funds, contributions, pledges).
                </p>

                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Key Capabilities & Access</h4>
                    <ul class="text-xs text-gray-700 space-y-1.5">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Full People & Household directory CRUD with notes
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Financial Management (Funds, Contributions, Pledges)
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Group Creation, Leader Assignment & Service Scheduling
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Broadcasting Mass Messages & Queueing Email Notifications
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('people.index') }}" class="text-xs font-semibold text-purple-700 hover:text-purple-900 flex items-center gap-1">
                    View Admin Section →
                </a>
                <button wire:click="switchToPersona('admin')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Switch to Admin Persona
                </button>
            </div>
        </div>

        <!-- Persona 2: Member -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl shrink-0">
                            👤
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Church Member</h2>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">Role: member</span>
                        </div>
                    </div>
                    @if($currentUser && $currentUser->role === 'member')
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">
                    Congregant view tailored for church members to access personal profiles, family household records, group involvement, serving commitments, and attendance history.
                </p>

                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Key Capabilities & Access</h4>
                    <ul class="text-xs text-gray-700 space-y-1.5">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Dedicated Member Portal (`/member-portal`)
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            View Household & Family members
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            View Small Groups & Serving Roster Assignments
                        </li>
                        <li class="flex items-center gap-2 text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Financial & Administrative management routes blocked (403)
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('members.portal') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-900 flex items-center gap-1">
                    View Member Portal →
                </a>
                <button wire:click="switchToPersona('member')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Switch to Member Persona
                </button>
            </div>
        </div>

        <!-- Persona 3: Leader of a Group -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xl shrink-0">
                            👥
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Leader of a Group</h2>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full">Role: leader</span>
                        </div>
                    </div>
                    @if($currentUser && $currentUser->role === 'leader')
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">
                    Ministry and group leadership access. Group leaders can edit their assigned group, manage small group rosters, track service attendance, and send group messages.
                </p>

                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Key Capabilities & Access</h4>
                    <ul class="text-xs text-gray-700 space-y-1.5">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Edit Own Led Group details & roster (`/groups/{group}/edit`)
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Manage Services & Record Attendance
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Access directory & household lists (read-only for non-led areas)
                        </li>
                        <li class="flex items-center gap-2 text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Financial data strictly restricted (Funds, Contributions, Pledges 403)
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('groups.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                    View Group Management →
                </a>
                <button wire:click="switchToPersona('leader')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Switch to Group Leader Persona
                </button>
            </div>
        </div>

        <!-- Persona 4: Guest -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xl shrink-0">
                            ⛪
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Guest Visitor</h2>
                            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full">Status: Unauthenticated</span>
                        </div>
                    </div>
                    @if(!Auth::check())
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Active
                        </span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 leading-relaxed">
                    Unauthenticated public visitor experience. Guests can view the church landing page, service times, vision statement, community features, and authentication links.
                </p>

                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Key Capabilities & Access</h4>
                    <ul class="text-xs text-gray-700 space-y-1.5">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Public Welcome & Landing Page (`/`)
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            View Church Features, Service Information & Mission
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Member Sign in (`/login`) & New Member Registration (`/register`)
                        </li>
                        <li class="flex items-center gap-2 text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            All internal system routes redirect to `/login`
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ url('/') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 flex items-center gap-1">
                    View Landing Page →
                </a>
                <button wire:click="switchToPersona('guest')" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Switch to Guest Persona (Logout)
                </button>
            </div>
        </div>

    </div>
</div>
