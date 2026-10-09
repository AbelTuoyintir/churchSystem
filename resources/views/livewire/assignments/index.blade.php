<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Serving Assignments</h2>
            <p class="text-sm text-gray-500">Manage volunteer schedules, roster assignments, and confirmation statuses.</p>
        </div>
        @can('create', App\Models\Assignment::class)
            <a href="{{ route('assignments.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-md shadow-sm">
                + New Assignment
            </a>
        @endcan
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-md text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by person or team name..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="teamId" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Teams</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                <option value="assigned">Assigned</option>
                <option value="confirmed">Confirmed</option>
                <option value="declined">Declined</option>
            </select>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Person</th>
                        <th class="px-6 py-3">Team & Position</th>
                        <th class="px-6 py-3">Service / Event</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $assignment->assigned_on?->format('M d, Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $assignment->person?->full_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">{{ $assignment->team?->name ?? '—' }}</span>
                                @if($assignment->position)
                                    <span class="text-xs text-gray-500 block">{{ $assignment->position->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($assignment->service)
                                    <span>Service: {{ $assignment->service->name }}</span>
                                @elseif($assignment->event)
                                    <span>Event: {{ $assignment->event->title }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClasses = match($assignment->status) {
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'declined' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize {{ $badgeClasses }}">
                                    {{ $assignment->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @can('update', $assignment)
                                    <a href="{{ route('assignments.edit', $assignment) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                @endcan
                                @can('delete', $assignment)
                                    <button wire:click="confirmDelete({{ $assignment->id }})" class="text-red-600 hover:text-red-900 font-medium ml-2">Delete</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">No serving assignments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $assignments->links() }}
        </div>
    </div>

    <!-- Deletion Confirmation Modal -->
    @if($confirmingAssignmentDeletion)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Delete Assignment</h3>
                <p class="text-sm text-gray-600">Are you sure you want to remove this serving assignment?</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('confirmingAssignmentDeletion', false)" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button wire:click="deleteAssignment" type="button" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                        Delete Assignment
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
