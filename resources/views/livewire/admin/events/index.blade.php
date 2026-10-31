<div class="space-y-6">
        <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-semibold text-gray-900">{{ $editingEventId ? 'Edit event' : 'Create event' }}</h2>
                <p class="mt-1 text-sm text-gray-500">Manage public church events and group activities.</p>
            </div>
            <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="event-title" class="mb-1 block text-sm font-medium text-gray-700">Title</label>
                    <input id="event-title" wire:model="title" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-category" class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                    <input id="event-category" wire:model="category" placeholder="Service, youth, outreach..." class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-starts" class="mb-1 block text-sm font-medium text-gray-700">Starts</label>
                    <input id="event-starts" type="datetime-local" wire:model="startsAt" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('startsAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-ends" class="mb-1 block text-sm font-medium text-gray-700">Ends</label>
                    <input id="event-ends" type="datetime-local" wire:model="endsAt" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('endsAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-location" class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <input id="event-location" wire:model="location" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="event-group" class="mb-1 block text-sm font-medium text-gray-700">Group</label>
                    <select id="event-group" wire:model="groupId" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        <option value="">Church-wide</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                    @error('groupId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="event-description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="event-description" wire:model="description" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm"></textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-wrap gap-6 md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" wire:model="allDay" class="rounded border-gray-300 text-indigo-700 focus:ring-indigo-600">All day</label>
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" wire:model="isPublic" class="rounded border-gray-300 text-indigo-700 focus:ring-indigo-600">Public event</label>
                </div>
                <div class="flex gap-2 md:col-span-2">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800 disabled:opacity-60">{{ $editingEventId ? 'Save changes' : 'Create event' }}</button>
                    @if ($editingEventId)
                        <button type="button" wire:click="resetForm" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                    @endif
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-base font-semibold text-gray-900">Church events</h2>
                <label for="event-search" class="sr-only">Search events</label>
                <input id="event-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search title or location" class="w-full rounded-md border-gray-300 text-sm shadow-sm sm:max-w-xs">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500"><tr><th class="px-5 py-3">Event</th><th class="px-5 py-3">Starts</th><th class="px-5 py-3">Location / group</th><th class="px-5 py-3">Visibility</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($events as $event)
                            <tr wire:key="admin-event-{{ $event->id }}">
                                <td class="px-5 py-3"><p class="font-medium text-gray-800">{{ $event->title }}</p><p class="mt-1 text-xs text-gray-500">{{ $event->category }}</p></td>
                                <td class="whitespace-nowrap px-5 py-3 text-gray-600">{{ $event->starts_at?->format('M j, Y g:i A') }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $event->location }}{{ $event->group ? ' · '.$event->group->name : '' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $event->is_public ? 'Public' : 'Private' }}</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right"><button type="button" wire:click="edit({{ $event->id }})" class="font-medium text-indigo-700 hover:text-indigo-900">Edit</button><button type="button" wire:click="delete({{ $event->id }})" wire:confirm="Delete this event?" class="ml-3 font-medium text-red-700 hover:text-red-900">Delete</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No events found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($events->hasPages())<div class="border-t border-gray-200 px-5 py-4">{{ $events->links() }}</div>@endif
        </section>
    </div>
