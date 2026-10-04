<div class="max-w-7xl mx-auto space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Welcome, {{ $person ? $person->full_name : $user->name }}!
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Member Portal - View your profile, family household, group memberships, serving assignments, and attendance history.
            </p>
        </div>
        <div class="inline-flex items-center px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold rounded-full capitalize">
            Role: {{ $user->role }}
        </div>
    </div>

    @if(!$person)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Your user account is not linked to a member profile yet. Please contact church administrator to link your profile.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info Card -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 font-bold text-2xl flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($person->first_name, 0, 1) . substr($person->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $person->full_name }}</h3>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                            Status: {{ $person->membership_status }}
                        </span>
                    </div>
                </div>

                <hr class="border-gray-200" />

                <div class="space-y-3 text-sm text-gray-700">
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Email</span>
                        <span class="text-gray-900">{{ $person->email ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Phone</span>
                        <span class="text-gray-900">{{ $person->phone ?: 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Address</span>
                        <span class="text-gray-900">
                            @if($person->address_line1)
                                {{ $person->address_line1 }}<br>
                                @if($person->address_line2){{ $person->address_line2 }}<br>@endif
                                {{ $person->city }}, {{ $person->state }} {{ $person->postal_code }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-500 uppercase">Date of Birth</span>
                        <span class="text-gray-900">{{ $person->date_of_birth ? $person->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Content Tabs / Sections -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Household / Family Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Household & Family
                    </h3>

                    @if($person->households->isEmpty())
                        <p class="text-sm text-gray-500 italic">No household currently associated.</p>
                    @else
                        @foreach($person->households as $household)
                            <div class="border rounded-md p-4 space-y-3 bg-gray-50">
                                <div class="font-semibold text-gray-900">{{ $household->name }}</div>
                                <div class="divide-y divide-gray-200">
                                    @foreach($household->people as $familyMember)
                                        <div class="py-2 flex justify-between items-center text-sm">
                                            <span class="font-medium text-gray-800">{{ $familyMember->full_name }}</span>
                                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded capitalize">
                                                {{ $familyMember->pivot->role ?? 'Member' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Groups Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Groups & Ministries
                    </h3>

                    @if($person->groups->isEmpty())
                        <p class="text-sm text-gray-500 italic">Not currently enrolled in any group.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($person->groups as $group)
                                <div class="border rounded-md p-4 bg-gray-50 space-y-1">
                                    <div class="font-semibold text-gray-900">{{ $group->name }}</div>
                                    <div class="text-xs text-gray-500 capitalize">Role: {{ $group->pivot->role ?? 'member' }}</div>
                                    @if($group->meeting_day || $group->meeting_time)
                                        <div class="text-xs text-gray-600">
                                            Meets: {{ ucfirst($group->meeting_day) }} {{ $group->meeting_time }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Serving Assignments -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Serving Assignments
                    </h3>

                    @if($person->assignments->isEmpty())
                        <p class="text-sm text-gray-500 italic">No serving assignments found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                    <tr>
                                        <th class="px-4 py-2">Date</th>
                                        <th class="px-4 py-2">Team</th>
                                        <th class="px-4 py-2">Position</th>
                                        <th class="px-4 py-2">Service</th>
                                        <th class="px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($person->assignments as $assignment)
                                        <tr>
                                            <td class="px-4 py-2 font-medium text-gray-900">{{ $assignment->assigned_on?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $assignment->team?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $assignment->position?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $assignment->service?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 capitalize">{{ $assignment->status ?? 'assigned' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Recent Attendance History -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Recent Attendance
                    </h3>

                    @if($person->attendances->isEmpty())
                        <p class="text-sm text-gray-500 italic">No recorded attendance entries.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                    <tr>
                                        <th class="px-4 py-2">Date</th>
                                        <th class="px-4 py-2">Service / Event</th>
                                        <th class="px-4 py-2">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($person->attendances->take(10) as $attendance)
                                        <tr>
                                            <td class="px-4 py-2 font-medium text-gray-900">{{ $attendance->attended_at?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $attendance->service?->name ?? 'Event' }}</td>
                                            <td class="px-4 py-2 font-semibold {{ $attendance->status === 'present' ? 'text-green-600' : 'text-red-600' }} capitalize">
                                                {{ $attendance->status }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
