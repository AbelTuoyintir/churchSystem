<div class="mx-auto max-w-7xl space-y-8">
    <header class="border-b border-gray-200 pb-5">
        <a href="{{ route('members.groups') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">&larr; All active groups</a>
        <div class="mt-4 flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="text-sm font-semibold uppercase text-emerald-700">{{ str_replace('_', ' ', $group->type) }}</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $group->name }}</h1>
                @if ($group->description)
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-gray-600">{{ $group->description }}</p>
                @endif
                <p class="mt-3 text-sm text-gray-600">{{ implode(' · ', array_filter([$group->meeting_day, $group->meeting_time ? \Illuminate\Support\Carbon::parse($group->meeting_time)->format('g:i A') : null, $group->location])) ?: 'Meeting details to be announced' }}</p>
            </div>
            <button type="button" wire:click="leaveGroup" wire:confirm="Leave {{ $group->name }}?" class="rounded-md border border-red-300 px-3 py-2 text-sm font-semibold text-red-800 hover:bg-red-50">Leave group</button>
        </div>
    </header>

    @if (session()->has('success'))
        <div role="status" class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <section aria-labelledby="roster-heading">
        <div class="flex items-baseline justify-between border-b border-gray-200 pb-3">
            <h2 id="roster-heading" class="text-xl font-bold text-gray-900">People</h2>
            <span class="text-sm text-gray-500">{{ $members->count() }} {{ \Illuminate\Support\Str::plural('person', $members->count()) }}</span>
        </div>
        <ul class="grid gap-x-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($members as $membership)
                @if ($membership->person)
                    <li wire:key="group-person-{{ $membership->person->id }}" class="flex items-center gap-3 border-b border-gray-200 py-4">
                        @if ($membership->person->photo_path)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($membership->person->photo_path) }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <div aria-hidden="true" class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">{{ strtoupper(substr($membership->person->first_name, 0, 1).substr($membership->person->last_name, 0, 1)) }}</div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-gray-900">{{ $membership->person->full_name }}</p>
                            <p class="text-xs capitalize text-gray-500">{{ str_replace('_', ' ', $membership->role) }}</p>
                            @if ($membership->person->directory_opt_in)
                                <p class="mt-1 truncate text-xs text-gray-600">
                                    @if ($membership->person->email) <span>{{ $membership->person->email }}</span> @endif
                                    @if ($membership->person->phone) <span>{{ $membership->person->email ? ' · ' : '' }}{{ $membership->person->phone }}</span> @endif
                                </p>
                            @endif
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </section>

    <div class="grid gap-10 lg:grid-cols-2">
        <section aria-labelledby="events-heading">
            <h2 id="events-heading" class="border-b border-gray-200 pb-3 text-xl font-bold text-gray-900">Upcoming events</h2>
            @forelse ($events as $event)
                <article wire:key="group-event-{{ $event->id }}" class="border-b border-gray-200 py-4">
                    <p class="text-xs font-semibold uppercase text-emerald-700">{{ $event->starts_at->format('D, M j · g:i A') }}</p>
                    <h3 class="mt-1 font-semibold text-gray-900">{{ $event->title }}</h3>
                    @if ($event->location)<p class="mt-1 text-sm text-gray-600">{{ $event->location }}</p>@endif
                    @if ($event->description)<p class="mt-2 text-sm leading-6 text-gray-600">{{ $event->description }}</p>@endif
                </article>
            @empty
                <p class="py-4 text-sm text-gray-600">No upcoming events have been posted.</p>
            @endforelse
        </section>

        <section aria-labelledby="announcements-heading">
            <h2 id="announcements-heading" class="border-b border-gray-200 pb-3 text-xl font-bold text-gray-900">Announcements</h2>
            @forelse ($announcements as $announcement)
                <article wire:key="group-announcement-{{ $announcement->id }}" class="border-b border-gray-200 py-4">
                    <p class="text-xs font-semibold uppercase text-gray-500">{{ $announcement->sent_at?->format('M j, Y') }}</p>
                    @if ($announcement->subject)<h3 class="mt-1 font-semibold text-gray-900">{{ $announcement->subject }}</h3>@endif
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $announcement->body }}</p>
                </article>
            @empty
                <p class="py-4 text-sm text-gray-600">No announcements have been posted.</p>
            @endforelse
        </section>
    </div>
</div>