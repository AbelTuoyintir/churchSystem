<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Events</h2>
            <p class="text-sm text-gray-500">Manage church events, special services, locations, and public calendars.</p>
        </div>
        @can('create', App\Models\Event::class)
            <a href="{{ route('events.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-md shadow-sm">
                + Create Event
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
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search events by title, description, location..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="category" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="isPublic" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Visibility</option>
                <option value="true">Public Only</option>
                <option value="false">Private Only</option>
            </select>
        </div>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Event Title</th>
                        <th class="px-6 py-3">Date & Time</th>
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Visibility</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $event->title }}
                                @if($event->description)
                                    <p class="text-xs text-gray-500 font-normal mt-0.5">{{ Str::limit($event->description, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $event->starts_at?->format('M d, Y g:i A') ?? 'N/A' }}
                                @if($event->ends_at)
                                    <span class="text-xs text-gray-500 font-normal block">to {{ $event->ends_at->format('M d, Y g:i A') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $event->location ?: '—' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $event->category ?: 'General' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($event->is_public)
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Public</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Private</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @can('update', $event)
                                    <a href="{{ route('events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                                @endcan
                                @can('delete', $event)
                                    <button wire:click="confirmDelete({{ $event->id }})" class="text-red-600 hover:text-red-900 font-medium ml-2">Delete</button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">No events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $events->links() }}
        </div>
    </div>

    <!-- Deletion Confirmation Modal -->
    @if($confirmingEventDeletion)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Delete Event</h3>
                <p class="text-sm text-gray-600">Are you sure you want to delete this event? This action cannot be undone.</p>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('confirmingEventDeletion', false)" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button wire:click="deleteEvent" type="button" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                        Delete Event
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
