<div class="mx-auto max-w-7xl space-y-6">
    @if (session()->has('success'))
        <div class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <header class="border-b border-gray-200 pb-5">
        <p class="text-sm font-semibold uppercase text-emerald-700">Community</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Church Events</h1>
        <p class="mt-1 text-sm text-gray-600">Public events you can view and register for.</p>
    </header>

    <div class="grid gap-x-8 gap-y-7 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($events as $event)
            @php $isRegistered = $registeredEventIds->contains($event->id); @endphp
            <article wire:key="event-{{ $event->id }}" class="flex min-h-56 flex-col border-b border-gray-200 pb-6">
                <p class="text-xs font-semibold uppercase text-gray-500">{{ $event->category ?: 'Event' }}</p>
                <h2 class="mt-1 text-lg font-bold text-gray-900">{{ $event->title }}</h2>
                @if ($event->group)<p class="mt-1 text-xs font-semibold text-emerald-800">{{ $event->group->name }}</p>@endif
                @if ($event->description)
                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-600">{{ $event->description }}</p>
                @endif
                <dl class="mt-4 space-y-1 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">When</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $event->starts_at->format('D, M j, Y · g:i A') }}{{ $event->ends_at ? ' - '.$event->ends_at->format('g:i A') : '' }}</dd>
                    </div>
                    @if ($event->location)
                        <div>
                            <dt class="text-xs font-semibold uppercase text-gray-500">Where</dt>
                            <dd class="mt-0.5 text-gray-900">{{ $event->location }}</dd>
                        </div>
                    @endif
                </dl>
                <div class="mt-auto flex flex-wrap items-center gap-3 pt-5">
                    @if (auth()->user()?->person_id)
                        @if ($isRegistered)
                            <span class="text-xs font-semibold uppercase text-emerald-800">Registered</span>
                            <button type="button" wire:click="cancelRsvp({{ $event->id }})" class="text-sm font-semibold text-red-700 hover:text-red-900">Cancel</button>
                        @else
                            <button type="button" wire:click="rsvp({{ $event->id }})" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-800">RSVP</button>
                        @endif
                    @else
                        <span class="text-sm text-gray-500">Link a member profile to register.</span>
                    @endif
                </div>
            </article>
        @empty
            <p class="py-10 text-sm text-gray-600">No upcoming public events.</p>
        @endforelse
    </div>

    <div>{{ $events->links() }}</div>
</div>
