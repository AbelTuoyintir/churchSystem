<div class="max-w-7xl mx-auto space-y-6">
    <!-- Session Flash Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

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
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold rounded-full capitalize">
                Role: {{ $user->role }}
            </div>
            @if($person)
                <button wire:click="openEditProfileModal" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-md shadow-sm transition">
                    Edit Contact Info
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-400 text-green-700 rounded shadow-sm text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded shadow-sm text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

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
                <div class="flex items-center justify-between">
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
                </div>

                <div class="flex justify-end">
                    <button wire:click="toggleEdit" type="button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-900 border border-indigo-200 px-3 py-1 rounded hover:bg-indigo-50">
                        {{ $isEditing ? 'Cancel Edit' : 'Edit Contact Info' }}
                    </button>
                </div>

                <hr class="border-gray-200" />

                @if(!$isEditing)
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
                @else
                    <form wire:submit.prevent="updateProfile" class="space-y-3 text-sm">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Phone</label>
                            <input type="text" wire:model="phone" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Address Line 1</label>
                            <input type="text" wire:model="address_line1" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('address_line1') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Address Line 2</label>
                            <input type="text" wire:model="address_line2" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('address_line2') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase">City</label>
                                <input type="text" wire:model="city" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('city') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase">State</label>
                                <input type="text" wire:model="state" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('state') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Postal Code</label>
                            <input type="text" wire:model="postal_code" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('postal_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase">Date of Birth</label>
                            <input type="date" wire:model="date_of_birth" class="w-full mt-1 px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('date_of_birth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-2 flex items-center gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs rounded hover:bg-indigo-700">
                                Save Profile
                            </button>
                            <button type="button" wire:click="toggleEdit" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold text-xs rounded hover:bg-gray-300">
                                Cancel
                            </button>
                        </div>
                    </form>
                @endif
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
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

    <!-- Edit Profile Modal -->
    @if ($editingProfile)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50 p-4">
            <div class="bg-white rounded-lg p-6 max-w-lg w-full shadow-xl space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Update Contact Details</h3>
                    <button wire:click="closeEditProfileModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label for="preferred_name" class="block text-sm font-medium text-gray-700">Preferred Name</label>
                        <input type="text" id="preferred_name" wire:model="preferred_name" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('preferred_name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" id="phone" wire:model="phone" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                            @error('phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="alternate_phone" class="block text-sm font-medium text-gray-700">Alternate Phone</label>
                            <input type="text" id="alternate_phone" wire:model="alternate_phone" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                            @error('alternate_phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="address_line1" class="block text-sm font-medium text-gray-700">Address Line 1</label>
                        <input type="text" id="address_line1" wire:model="address_line1" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('address_line1') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="address_line2" class="block text-sm font-medium text-gray-700">Address Line 2</label>
                        <input type="text" id="address_line2" wire:model="address_line2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('address_line2') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label for="city" class="block text-xs font-medium text-gray-700">City</label>
                            <input type="text" id="city" wire:model="city" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2 py-1.5 border">
                            @error('city') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-xs font-medium text-gray-700">State</label>
                            <input type="text" id="state" wire:model="state" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2 py-1.5 border">
                            @error('state') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-xs font-medium text-gray-700">Postal Code</label>
                            <input type="text" id="postal_code" wire:model="postal_code" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2 py-1.5 border">
                            @error('postal_code') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gray-200">
                        <label class="block text-sm font-medium text-gray-700">Communication Preferences</label>
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center text-xs text-gray-700">
                                <input type="checkbox" wire:model="email_opt_in" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4 mr-2">
                                Receive Emails
                            </label>

                            <label class="inline-flex items-center text-xs text-gray-700">
                                <input type="checkbox" wire:model="sms_opt_in" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4 mr-2">
                                Receive SMS
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeEditProfileModal" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
