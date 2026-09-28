<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $group && $group->exists ? 'Edit Group: ' . $group->name : 'Create Group' }}
            </h2>
            <a href="{{ route('groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session()->has('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Group Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Group Name *</label>
                        <input id="name" type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Group Type *</label>
                        <select id="type" wire:model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                            <option value="small_group">Small Group</option>
                            <option value="ministry">Ministry</option>
                            <option value="committee">Committee</option>
                            <option value="class">Class</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border"></textarea>
                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="leader_id" class="block text-sm font-medium text-gray-700">Group Leader</label>
                        <div class="mt-1 space-y-2">
                            <input type="text" wire:model.live.debounce.300ms="leaderSearch" placeholder="Search leader by name..." class="w-full text-xs rounded-md border-gray-300 shadow-sm px-2 py-1 border mb-1">
                            <select id="leader_id" wire:model="leader_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="">Select Leader (None)</option>
                                @foreach ($allPeople as $person)
                                    <option value="{{ $person->id }}">{{ $person->first_name }} {{ $person->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('leader_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="meeting_day" class="block text-sm font-medium text-gray-700">Meeting Day</label>
                        <input id="meeting_day" type="text" wire:model="meeting_day" placeholder="e.g. Wednesday, Sunday" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('meeting_day') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="meeting_time" class="block text-sm font-medium text-gray-700">Meeting Time</label>
                        <input id="meeting_time" type="time" wire:model="meeting_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('meeting_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input id="location" type="text" wire:model="location" placeholder="e.g. Room 102 / Online" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                        @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center mt-6">
                        <label for="is_active" class="inline-flex items-center cursor-pointer">
                            <input id="is_active" type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Is Active</span>
                        </label>
                    </div>
                </div>

                @can($group && $group->exists ? 'update' : 'create', $group)
                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none transition">
                            {{ $group && $group->exists ? 'Update Group' : 'Create Group' }}
                        </button>
                    </div>
                @endcan
            </form>
        </div>

        <!-- Members Section (Only shown on edit mode when group exists) -->
        @if ($group && $group->exists)
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-3">Group Members</h3>

                @if (session()->has('member_success'))
                    <div class="p-3 bg-green-100 border border-green-400 text-green-700 text-sm rounded-md">
                        {{ session('member_success') }}
                    </div>
                @endif

                @can('update', $group)
                    <form wire:submit.prevent="attachMember" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-gray-50 p-4 rounded-md border">
                        <div>
                            <label for="selectedPersonId" class="block text-xs font-medium text-gray-700 mb-1">Select Person *</label>
                            <select id="selectedPersonId" wire:model="selectedPersonId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="">Select Person</option>
                                @foreach ($allPeople as $person)
                                    <option value="{{ $person->id }}">{{ $person->first_name }} {{ $person->last_name }}</option>
                                @endforeach
                            </select>
                            @error('selectedPersonId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="memberRole" class="block text-xs font-medium text-gray-700 mb-1">Role *</label>
                            <select id="memberRole" wire:model="memberRole" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="leader">Leader</option>
                                <option value="co_leader">Co-Leader</option>
                                <option value="member">Member</option>
                            </select>
                            @error('memberRole') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="joinedAt" class="block text-xs font-medium text-gray-700 mb-1">Joined Date *</label>
                            <input id="joinedAt" type="date" wire:model="joinedAt" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                            @error('joinedAt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase hover:bg-indigo-700">
                                Attach Member
                            </button>
                        </div>
                    </form>
                @endcan

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined At</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($currentMembers as $member)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $member->person ? $member->person->first_name . ' ' . $member->person->last_name : '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                        {{ str_replace('_', ' ', $member->role) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $member->joined_at ? \Illuminate\Support\Carbon::parse($member->joined_at)->format('Y-m-d') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @can('update', $group)
                                            <button wire:click="confirmDetachMember({{ $member->id }})" class="text-red-600 hover:text-red-900">Detach</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-gray-500 text-sm">
                                        No active members attached to this group.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Confirmation Modal for Detach Member -->
    @if ($confirmingMemberDetach)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Detach</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to detach this member? (This will set their left_at date rather than deleting the record.)</p>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingMemberDetach', false)" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                    <button wire:click="detachMember" class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-md">Detach Member</button>
                </div>
            </div>
        </div>
    @endif
</div>
