<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Attendance
            </h2>
            @can('create', App\Models\Attendance::class)
                <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                    + Record Attendance
                </button>
            @endcan
        </div>
    </x-slot>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
        @if (session()->has('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filters Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 bg-gray-50 p-4 rounded-md border border-gray-200">
            <div>
                <label for="dateFrom" class="block text-xs font-medium text-gray-700 mb-1">Date From</label>
                <input id="dateFrom" type="date" wire:model.live="dateFrom" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
            </div>

            <div>
                <label for="dateTo" class="block text-xs font-medium text-gray-700 mb-1">Date To</label>
                <input id="dateTo" type="date" wire:model.live="dateTo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
            </div>

            <div>
                <label for="serviceId" class="block text-xs font-medium text-gray-700 mb-1">Service</label>
                <select id="serviceId" wire:model.live="serviceId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
                    <option value="">All Services</option>
                    @foreach ($allServices as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="groupId" class="block text-xs font-medium text-gray-700 mb-1">Group</label>
                <select id="groupId" wire:model.live="groupId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
                    <option value="">All Groups</option>
                    @foreach ($allGroups as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="statusFilter" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" wire:model.live="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
                    <option value="">All Statuses</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="excused">Excused</option>
                    <option value="late">Late</option>
                </select>
            </div>
        </div>

        <!-- Bulk Action Controls -->
        @can('create', App\Models\Attendance::class)
            @if (!empty($selectedAttendanceIds))
                <div class="flex items-center gap-3 bg-indigo-50 p-3 rounded-md border border-indigo-200">
                    <span class="text-sm font-medium text-indigo-900">
                        {{ count($selectedAttendanceIds) }} row(s) selected:
                    </span>
                    <button wire:click="bulkMarkStatus('present')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-md shadow-sm">
                        Mark present
                    </button>
                    <button wire:click="bulkMarkStatus('absent')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md shadow-sm">
                        Mark absent
                    </button>
                </div>
            @endif
        @endcan

        <!-- Attendance Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @can('create', App\Models\Attendance::class)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4">
                            </th>
                        @endcan
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Context</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Headcount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                        <tr>
                            @can('create', App\Models\Attendance::class)
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <input type="checkbox" value="{{ $attendance->id }}" wire:model.live="selectedAttendanceIds" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4">
                                </td>
                            @endcan
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $attendance->attended_on ? $attendance->attended_on->format('Y-m-d') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $attendance->person ? $attendance->person->full_name : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $this->resolveContext($attendance) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($attendance->status === 'present')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 capitalize">
                                        Present
                                    </span>
                                @elseif ($attendance->status === 'absent')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 capitalize">
                                        Absent
                                    </span>
                                @elseif ($attendance->status === 'excused')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 capitalize">
                                        Excused
                                    </span>
                                @elseif ($attendance->status === 'late')
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">
                                        Late
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                        {{ $attendance->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attendance->person_id ? ($attendance->headcount ?? 1) : ($attendance->headcount ?? '—') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                @can('update', $attendance)
                                    <button wire:click="openEditModal({{ $attendance->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                @endcan
                                @can('delete', $attendance)
                                    <button wire:click="confirmDelete({{ $attendance->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->can('create', App\Models\Attendance::class) ? 7 : 6 }}" class="px-6 py-8 text-center text-gray-500">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $attendances->links() }}
        </div>
    </div>

    <!-- Create/Edit Form Modal -->
    @if ($showingFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50 p-4">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full shadow-xl space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $editingAttendanceId ? 'Edit Attendance Record' : 'Record Attendance' }}
                    </h3>
                    <button wire:click="closeFormModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="saveAttendance" class="space-y-4">
                    <!-- Person Selector (Searchable & Nullable) -->
                    <div>
                        <label for="person_id" class="block text-sm font-medium text-gray-700">Person (Leave empty for anonymous headcount)</label>
                        <div class="mt-1 space-y-2">
                            <input type="text" wire:model.live.debounce.300ms="personSearch" placeholder="Search person by name..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-3 py-1.5 border">
                            <select id="person_id" wire:model.live="person_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="">— Anonymous / Headcount Only —</option>
                                @foreach ($people as $p)
                                    <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('person_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Context: Service / Event / Group -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="modal_service_id" class="block text-sm font-medium text-gray-700">Service</label>
                            <select id="modal_service_id" wire:model="service_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="">None</option>
                                @foreach ($services as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('service_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="modal_event_id" class="block text-sm font-medium text-gray-700">Event</label>
                            <select id="modal_event_id" wire:model="event_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="">None</option>
                                @foreach ($events as $e)
                                    <option value="{{ $e->id }}">{{ $e->title }}</option>
                                @endforeach
                            </select>
                            @error('event_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="modal_group_id" class="block text-sm font-medium text-gray-700">Group</label>
                            <select id="modal_group_id" wire:model="group_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="">None</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('group_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Date & Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="modal_attended_on" class="block text-sm font-medium text-gray-700">Attended On *</label>
                            <input type="date" id="modal_attended_on" wire:model="attended_on" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                            @error('attended_on') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="modal_status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="modal_status" wire:model="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="excused">Excused</option>
                                <option value="late">Late</option>
                            </select>
                            @error('status') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Headcount: Only shown when person_id is empty/null! -->
                    @if (empty($person_id))
                        <div>
                            <label for="modal_headcount" class="block text-sm font-medium text-gray-700">Headcount *</label>
                            <input type="number" id="modal_headcount" min="1" wire:model="headcount" placeholder="Enter total headcount..." class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                            @error('headcount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Notes -->
                    <div>
                        <label for="modal_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="modal_notes" wire:model="notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border" placeholder="Optional notes..."></textarea>
                        @error('notes') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeFormModal" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50 p-4">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Confirm Deletion</h3>
                <p class="text-sm text-gray-600">Are you sure you want to delete this attendance record? This action cannot be undone.</p>
                <div class="flex justify-end gap-3 pt-4">
                    <button wire:click="$set('confirmingDeletion', false)" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button wire:click="deleteAttendance" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                        Delete Record
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
