<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $event && $event->exists ? 'Edit Event: ' . $event->title : 'Create New Event' }}
        </h2>
        <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium">← Back to Events</a>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-md text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Event Title *</label>
                <input type="text" wire:model="title" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                @error('title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea wire:model="description" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                @error('description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Starts At *</label>
                <input type="datetime-local" wire:model="starts_at" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                @error('starts_at') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ends At</label>
                <input type="datetime-local" wire:model="ends_at" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                @error('ends_at') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
                <input type="text" wire:model="location" placeholder="Main Sanctuary, Fellowship Hall..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                @error('location') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <input type="text" wire:model="category" placeholder="Worship, Youth, Outreach..." class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                @error('category') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="all_day" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700 font-medium">All Day Event</span>
                </label>
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="is_public" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ml-2 text-sm text-gray-700 font-medium">Public Calendar Event</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">Save Event</button>
        </div>
    </form>
</div>
