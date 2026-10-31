<div class="space-y-6">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">{{ $editingAssignmentId ? 'Edit assignment' : 'Schedule a team member' }}</h2>
                <p class="mt-1 text-sm text-gray-500">Assign people to a service, event, team, or position.</p>
            </div>

            <form wire:submit="save" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label for="assignment-team" class="mb-1 block text-sm font-medium text-gray-700">Team</label>
                    <select id="assignment-team" wire:model.live="teamId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">Choose team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('teamId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="assignment-position" class="mb-1 block text-sm font-medium text-gray-700">Position</label>
                    <select id="assignment-position" wire:model="positionId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">No specific position</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                    @error('positionId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="assignment-person" class="mb-1 block text-sm font-medium text-gray-700">Person</label>
                    <select id="assignment-person" wire:model="personId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">Choose person</option>
                        @foreach ($people as $person)
                            <option value="{{ $person->id }}">{{ $person->full_name }}</option>
                        @endforeach
                    </select>
                    @error('personId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="assignment-service" class="mb-1 block text-sm font-medium text-gray-700">Service</label>
                    <select id="assignment-service" wire:model="serviceId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">No service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                    @error('serviceId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="assignment-event" class="mb-1 block text-sm font-medium text-gray-700">Event</label>
                    <select id="assignment-event" wire:model="eventId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">No event</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }} · {{ $event->starts_at?->format('M j') }}</option>
                        @endforeach
                    </select>
                    @error('eventId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="assignment-date" class="mb-1 block text-sm font-medium text-gray-700">Assignment date</label>
                    <input id="assignment-date" type="date" wire:model="assignedOn" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('assignedOn') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="assignment-status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select id="assignment-status" wire:model="status" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="declined">Declined</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 xl:col-span-3">
                    <label for="assignment-notes" class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="assignment-notes" wire:model="notes" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-2 md:col-span-2 xl:col-span-3">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 disabled:opacity-60">{{ $editingAssignmentId ? 'Save changes' : 'Create assignment' }}</button>
                    @if ($editingAssignmentId)
                        <button type="button" wire:click="resetForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                    @endif
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-semibold text-gray-900">Assignments</h2>
                <label for="assignment-search" class="sr-only">Search assignments</label>
                <input id="assignment-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search people or teams" class="w-full rounded-md border-gray-300 text-sm shadow-sm sm:max-w-xs">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                        <tr><th class="px-5 py-3">Date</th><th class="px-5 py-3">Person</th><th class="px-5 py-3">Team / position</th><th class="px-5 py-3">Service / event</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($assignments as $assignment)
                            <tr wire:key="assignment-{{ $assignment->id }}">
                                <td class="whitespace-nowrap px-5 py-3 text-gray-600">{{ $assignment->assigned_on?->format('M j, Y') }}</td>
                                <td class="whitespace-nowrap px-5 py-3 font-medium text-gray-800">{{ $assignment->person?->full_name }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $assignment->team?->name }}{{ $assignment->position ? ' · '.$assignment->position->name : '' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $assignment->service?->name ?? $assignment->event?->title ?? 'Unlinked' }}</td>
                                <td class="whitespace-nowrap px-5 py-3 capitalize text-gray-600">{{ str_replace('_', ' ', $assignment->status) }}</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right">
                                    <button type="button" wire:click="edit({{ $assignment->id }})" class="font-medium text-indigo-700 hover:text-indigo-900">Edit</button>
                                    <button type="button" wire:click="delete({{ $assignment->id }})" wire:confirm="Delete this assignment?" class="ml-3 font-medium text-red-700 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No assignments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($assignments->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">{{ $assignments->links() }}</div>
            @endif
        </section>
    </div>
