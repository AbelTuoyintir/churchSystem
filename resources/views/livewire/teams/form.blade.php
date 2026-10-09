<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $team && $team->exists ? 'Edit Team: ' . $team->name : 'Create New Team' }}
        </h2>
        <a href="{{ route('teams.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">← Back to Teams</a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-md text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Team Main Form -->
    <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Team Name *</label>
                <input type="text" wire:model="name" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea wire:model="description" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Team Leader</label>
                <select wire:model="leader_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Select Team Leader --</option>
                    @foreach($allPeople as $person)
                        <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                    @endforeach
                </select>
                @error('leader_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center pt-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700 font-medium">Active Team</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('teams.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Save Team</button>
        </div>
    </form>

    @if($team && $team->exists)
        <!-- Positions Section -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
            <h3 class="text-lg font-bold text-gray-800">Team Positions</h3>

            @if (session()->has('position_success'))
                <div class="p-3 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-md text-xs font-medium">
                    {{ session('position_success') }}
                </div>
            @endif

            <form wire:submit.prevent="addPosition" class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-md border border-gray-200">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Position Name *</label>
                    <input type="text" wire:model="positionName" placeholder="e.g. Lead Vocalist" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                    @error('positionName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Slots Per Service</label>
                    <input type="number" wire:model="slotsPerService" min="1" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('slotsPerService') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Add Position</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-2">Position</th>
                            <th class="px-4 py-2">Slots / Service</th>
                            <th class="px-4 py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($positions as $pos)
                            <tr>
                                <td class="px-4 py-2 font-semibold text-gray-900">{{ $pos->name }}</td>
                                <td class="px-4 py-2">{{ $pos->slots_per_service }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button wire:click="confirmDeletePosition({{ $pos->id }})" type="button" class="text-red-600 hover:text-red-900 text-xs font-medium">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 italic">No positions created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Team Roster Members Section -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
            <h3 class="text-lg font-bold text-gray-800">Team Roster Members</h3>

            @if (session()->has('member_success'))
                <div class="p-3 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-md text-xs font-medium">
                    {{ session('member_success') }}
                </div>
            @endif

            <form wire:submit.prevent="attachMember" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-md border border-gray-200">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Select Person *</label>
                    <select wire:model="selectedPersonId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- Choose Person --</option>
                        @foreach($allPeople as $person)
                            <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                        @endforeach
                    </select>
                    @error('selectedPersonId') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Position</label>
                    <select wire:model="selectedPositionId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- General Member --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Joined Date</label>
                    <input type="date" wire:model="joinedAt" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Attach Member</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-2">Person Name</th>
                            <th class="px-4 py-2">Position</th>
                            <th class="px-4 py-2">Joined</th>
                            <th class="px-4 py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($currentMembers as $m)
                            <tr>
                                <td class="px-4 py-2 font-semibold text-gray-900">{{ $m->person?->full_name }}</td>
                                <td class="px-4 py-2">{{ $m->position?->name ?? 'General' }}</td>
                                <td class="px-4 py-2">{{ $m->joined_at?->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button wire:click="confirmDetachMember({{ $m->id }})" type="button" class="text-red-600 hover:text-red-900 text-xs font-medium">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500 italic">No active roster members.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Delete Position Confirmation Modal -->
    @if($confirmingPositionDelete)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Remove Position</h3>
                <p class="text-sm text-gray-600">Are you sure you want to remove this position from the team?</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('confirmingPositionDelete', false)" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</button>
                    <button wire:click="deletePosition" type="button" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Remove Position</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Detach Member Confirmation Modal -->
    @if($confirmingMemberDetach)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Remove Team Member</h3>
                <p class="text-sm text-gray-600">Are you sure you want to mark this person as left from the team?</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('confirmingMemberDetach', false)" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</button>
                    <button wire:click="detachMember" type="button" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Remove Member</button>
                </div>
            </div>
        </div>
    @endif
</div>
