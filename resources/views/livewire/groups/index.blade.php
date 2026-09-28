<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Groups
            </h2>
            @can('create', App\Models\Group::class)
                <a href="{{ route('groups.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                    + Add Group
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search and Filters -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Name</label>
                <input id="search" type="text" wire:model.live.debounce.300ms="search" placeholder="Search group name..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Filter by Type</label>
                <select id="type" wire:model.live="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    <option value="">All Types</option>
                    <option value="small_group">Small Group</option>
                    <option value="ministry">Ministry</option>
                    <option value="committee">Committee</option>
                    <option value="class">Class</option>
                </select>
            </div>

            <div>
                <label for="isActive" class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                <select id="isActive" wire:model.live="isActive" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                    <option value="">All Statuses</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Groups Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leader</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting Day / Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($groups as $group)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $group->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">
                                    {{ str_replace('_', ' ', $group->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $group->leader ? $group->leader->first_name . ' ' . $group->leader->last_name : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ implode(' ', array_filter([$group->meeting_day, $group->meeting_time])) ?: '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $group->members_count }} member(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($group->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @can('update', $group)
                                    <a href="{{ route('groups.edit', $group) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @if ($group->is_active)
                                        <button wire:click="confirmInactivate({{ $group->id }})" class="text-amber-600 hover:text-amber-900">Mark inactive</button>
                                    @endif
                                @else
                                    <a href="{{ route('groups.edit', $group) }}" class="text-gray-600 hover:text-gray-900">View</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No groups found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $groups->links() }}
        </div>
    </div>

    <!-- Confirmation Modal for Mark Inactive -->
    @if ($confirmingInactivation)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Action</h3>
                <p class="text-sm text-gray-600 mb-6">Are you sure you want to mark this group as inactive?</p>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('confirmingInactivation', false)" class="px-4 py-2 text-sm text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                    <button wire:click="markInactive" class="px-4 py-2 text-sm text-white bg-amber-600 hover:bg-amber-700 rounded-md">Mark Inactive</button>
                </div>
            </div>
        </div>
    @endif
</div>
