<div>
<div class="mx-auto max-w-7xl space-y-6">
    <header class="border-b border-gray-200 pb-5"><p class="text-sm font-semibold uppercase text-emerald-700">Leadership</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Group Events</h1></header>
    @if (session()->has('success'))<p class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</p>@endif
    @if ($groups->isNotEmpty())
        <section class="grid gap-8 lg:grid-cols-[minmax(18rem,0.8fr)_minmax(0,1.2fr)]">
            <form wire:submit="save" class="space-y-4 border-b border-gray-200 pb-6 lg:border-b-0 lg:border-r lg:pr-8">
                <h2 class="text-base font-bold text-gray-900">{{ $eventId ? 'Edit event' : 'Create group event' }}</h2>
                <label class="block text-sm font-medium text-gray-700">Group<select wire:model="groupId" class="mt-1 block w-full rounded-md border-gray-300 text-sm">@foreach ($groups as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach</select></label>
                <label class="block text-sm font-medium text-gray-700">Title<input wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>@error('title')<span class="text-sm text-red-700">{{ $message }}</span>@enderror
                <label class="block text-sm font-medium text-gray-700">Description<textarea wire:model="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></textarea></label>
                <div class="grid gap-3 sm:grid-cols-2"><label class="block text-sm font-medium text-gray-700">Starts<input type="datetime-local" wire:model="startsAt" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label><label class="block text-sm font-medium text-gray-700">Ends<input type="datetime-local" wire:model="endsAt" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label></div>
                <label class="block text-sm font-medium text-gray-700">Location<input wire:model="location" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
                <label class="block text-sm font-medium text-gray-700">Category<input wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input type="checkbox" wire:model="allDay" class="rounded border-gray-300">All day</label>
                <div class="flex gap-3"><button class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Save event</button>@if ($eventId)<button type="button" wire:click="resetForm" class="px-3 py-2 text-sm font-semibold text-gray-600">Cancel</button>@endif</div>
            </form>
            <section><h2 class="text-base font-bold text-gray-900">Events for your groups</h2><div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">
                @forelse ($events as $event)
                    <article class="flex flex-wrap items-center justify-between gap-4 py-4" wire:key="group-event-{{ $event->id }}"><div><p class="font-semibold text-gray-900">{{ $event->title }}</p><p class="mt-1 text-sm text-gray-600">{{ $event->group->name }} · {{ $event->starts_at->format('M j, Y g:i A') }} · {{ $event->location ?: 'No location' }}</p></div><div class="flex gap-3"><button type="button" wire:click="edit({{ $event->id }})" class="text-sm font-semibold text-indigo-700">Edit</button><button type="button" wire:click="delete({{ $event->id }})" wire:confirm="Delete this event?" class="text-sm font-semibold text-rose-700">Delete</button></div></article>
                @empty<p class="py-6 text-sm text-gray-600">No group events yet.</p>@endforelse
            </div><div class="mt-4">{{ $events->links() }}</div></section>
        </section>
    @else
        <p class="py-10 text-sm text-gray-600">No groups are assigned to your leadership yet.</p>
    @endif
</div>
</div>
