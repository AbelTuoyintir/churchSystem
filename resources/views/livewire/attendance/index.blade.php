<div>
    @php
        $statusStyles = [
            'present' => 'bg-green-100 text-green-800',
            'absent'  => 'bg-red-100 text-red-800',
            'excused' => 'bg-yellow-100 text-yellow-800',
            'late'    => 'bg-blue-100 text-blue-800',
        ];
        $canCreate = auth()->user()->can('create', App\Models\Attendance::class);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendance</h2>
                <p class="text-xs text-gray-500 mt-0.5">Track who showed up, and how many.</p>
            </div>
            @can('create', App\Models\Attendance::class)
                <button wire:click="openCreateModal" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                    + Record Attendance
                </button>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session()->has('success'))
            <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Left: Filters sidebar -->
            <aside class="w-full lg:w-64 lg:shrink-0 lg:sticky lg:top-4 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Filters</h3>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <span class="block text-xs font-medium text-gray-700 mb-1">Date range</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="dateFrom" class="sr-only">Date From</label>
                                <input id="dateFrom" type="date" wire:model.live="dateFrom" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
                            </div>
                            <div>
                                <label for="dateTo" class="sr-only">Date To</label>
                                <input id="dateTo" type="date" wire:model.live="dateTo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs px-2 py-1.5 border">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-1 text-[10px] text-gray-400 uppercase tracking-wider">
                            <span>From</span><span>To</span>
                        </div>
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
            </aside>

            <!-- Right: Records -->
            <section class="flex-1 min-w-0 w-full space-y-4">

                <!-- Bulk actions -->
                @can('create', App\Models\Attendance::class)
                    @if (!empty($selectedAttendanceIds))
                        <div class="flex flex-wrap items-center gap-3 bg-indigo-50 p-3 rounded-md border border-indigo-200">
                            <span class="text-sm font-medium text-indigo-900">
                                {{ count($selectedAttendanceIds) }} row(s) selected
                            </span>
                            <div class="flex gap-2 sm:ml-auto">
                                <button wire:click="bulkMarkStatus('present')" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-md shadow-sm">
                                    Mark present
                                </button>
                                <button wire:click="bulkMarkStatus('absent')" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md shadow-sm">
                                    Mark absent
                                </button>
                            </div>
                        </div>
                    @endif
                @endcan

                <!-- Desktop / tablet table -->
                <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @can('create', App\Models\Attendance::class)
                                        <th class="px-4 py-3 text-left w-10">
                                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4">
                                        </th>
                                    @endcan
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Person</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Context</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Headcount</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($attendances as $attendance)
                                    <tr class="hover:bg-gray-50">
                                        @can('create', App\Models\Attendance::class)
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="checkbox" value="{{ $attendance->id }}" wire:model.live="selectedAttendanceIds" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4">
                                            </td>
                                        @endcan
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attendance->person ? $attendance->person->full_name : 'Anonymous headcount' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $attendance->attended_on ? $attendance->attended_on->format('D, M j, Y') : '—' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $this->resolveContext($attendance) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize {{ $statusStyles[$attendance->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-center">
                                            {{ $attendance->person_id ? ($attendance->headcount ?? 1) : ($attendance->headcount ?? '—') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-3">
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
                                        <td colspan="{{ $canCreate ? 6 : 5 }}" class="px-6 py-10 text-center text-gray-500">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden space-y-3">
                    @forelse ($attendances as $attendance)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    @can('create', App\Models\Attendance::class)
                                        <input type="checkbox" value="{{ $attendance->id }}" wire:model.live="selectedAttendanceIds" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4">
                                    @endcan
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $attendance->person ? $attendance->person->full_name : 'Anonymous headcount' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $attendance->attended_on ? $attendance->attended_on->format('D, M j, Y') : '—' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize {{ $statusStyles[$attendance->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $attendance->status }}
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                                <span>{{ $this->resolveContext($attendance) }}</span>
                                <span>Headcount: <span class="font-medium text-gray-700">{{ $attendance->person_id ? ($attendance->headcount ?? 1) : ($attendance->headcount ?? '—') }}</span></span>
                            </div>

                            <div class="mt-3 pt-3 border-t border-gray-100 flex justify-end gap-4 text-sm font-medium">
                                @can('update', $attendance)
                                    <button wire:click="openEditModal({{ $attendance->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                @endcan
                                @can('delete', $attendance)
                                    <button wire:click="confirmDelete({{ $attendance->id }})" class="text-red-600 hover:text-red-900">Delete</button>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500">
                            No attendance records found.
                        </div>
                    @endforelse
                </div>

                <div>
                    {{ $attendances->links() }}
                </div>
            </section>
        </div>
    </div>

    <!-- Create/Edit Form Modal -->
    @if ($showingFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-900 bg-opacity-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full shadow-xl overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $editingAttendanceId ? 'Edit Attendance Record' : 'Record Attendance' }}
                    </h3>
                    <button wire:click="closeFormModal" aria-label="Close modal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit.prevent="saveAttendance">
                    <div class="px-6 py-5 space-y-6">

                        <!-- 1. Who -->
                        <fieldset class="space-y-2">
                            <legend class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">1 · Who</legend>
                            <label for="person_id" class="block text-sm font-medium text-gray-700">Person <span class="text-gray-400 font-normal">(leave empty for anonymous headcount)</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="text" wire:model.live.debounce.300ms="personSearch" placeholder="Search person by name..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                <select id="person_id" wire:model.live="person_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                    <option value="">— Anonymous / Headcount Only —</option>
                                    @foreach ($people as $p)
                                        <option value="{{ $p->id }}">{{ $p->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('person_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror

                            @if (empty($person_id))
                                <div class="pt-2">
                                    <label for="modal_headcount" class="block text-sm font-medium text-gray-700">Headcount *</label>
                                    <input type="number" id="modal_headcount" min="1" wire:model="headcount" placeholder="Enter total headcount..." class="mt-1 w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                    @error('headcount') <span class="text-red-600 text-xs block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </fieldset>

                        <!-- 2. When & Status -->
                        <fieldset class="space-y-3">
                            <legend class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">2 · When &amp; status</legend>
                            <div>
                                <label for="modal_attended_on" class="block text-sm font-medium text-gray-700">Attended On *</label>
                                <input type="date" id="modal_attended_on" wire:model="attended_on" class="mt-1 w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border">
                                @error('attended_on') <span class="text-red-600 text-xs block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-700 mb-1">Status *</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <label class="cursor-pointer">
                                        <input type="radio" value="present" wire:model="status" class="peer sr-only">
                                        <span class="block text-center text-sm font-medium px-3 py-2 rounded-md border border-gray-300 text-gray-700 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white peer-focus:ring ring-green-300">Present</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" value="absent" wire:model="status" class="peer sr-only">
                                        <span class="block text-center text-sm font-medium px-3 py-2 rounded-md border border-gray-300 text-gray-700 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white peer-focus:ring ring-red-300">Absent</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" value="excused" wire:model="status" class="peer sr-only">
                                        <span class="block text-center text-sm font-medium px-3 py-2 rounded-md border border-gray-300 text-gray-700 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 peer-checked:text-white peer-focus:ring ring-yellow-300">Excused</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" value="late" wire:model="status" class="peer sr-only">
                                        <span class="block text-center text-sm font-medium px-3 py-2 rounded-md border border-gray-300 text-gray-700 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white peer-focus:ring ring-blue-300">Late</span>
                                    </label>
                                </div>
                                @error('status') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </fieldset>

                        <!-- 3. Context -->
                        <fieldset class="space-y-2">
                            <legend class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">3 · Context</legend>
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
                        </fieldset>

                        <!-- Notes -->
                        <div>
                            <label for="modal_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="modal_notes" wire:model="notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2 border" placeholder="Optional notes..."></textarea>
                            @error('notes') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50 border-t border-gray-200">
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