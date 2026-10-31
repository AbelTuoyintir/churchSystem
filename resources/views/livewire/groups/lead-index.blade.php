<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <p class="text-sm font-semibold uppercase text-emerald-700">Leadership</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">My Groups</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $groups->count() }} group{{ $groups->count() === 1 ? '' : 's' }} you lead</p>
        </div>
        <a href="{{ route('members.groups') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Browse all groups</a>
    </header>

    <div class="divide-y divide-gray-200 border-y border-gray-200">
        @forelse ($groups as $group)
            <article class="grid gap-4 py-5 md:grid-cols-[minmax(0,1fr)_auto] md:items-center" wire:key="lead-group-{{ $group->id }}">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-900">{{ $group->name }}</h2>
                        <span class="text-xs font-semibold uppercase text-gray-500">{{ str_replace('_', ' ', $group->type) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">{{ $group->description ?: 'No group description.' }}</p>
                    <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-4">
                        <div><dt class="text-xs font-semibold uppercase text-gray-500">Roster</dt><dd class="mt-1 text-sm font-semibold text-gray-900">{{ $group->members_count }} active</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-gray-500">Last attendance</dt><dd class="mt-1 text-sm text-gray-900">{{ $group->last_meeting_on ? \Illuminate\Support\Carbon::parse($group->last_meeting_on)->format('M j') . ' · ' . $group->last_meeting_attendance . ' present' : 'Not recorded' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-gray-500">No attendance, 30 days</dt><dd class="mt-1 text-sm text-gray-900">{{ $group->members_without_attendance }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-gray-500">Open serving slots</dt><dd class="mt-1 text-sm text-gray-900">{{ $group->open_serving_slots }}</dd></div>
                    </dl>
                </div>
                <div class="flex flex-wrap gap-3 md:justify-end">
                    <a href="{{ route('groups.lead.directory') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Directory</a>
                    <a href="{{ route('groups.edit', $group) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Manage group</a>
                    <a href="{{ route('groups.lead.attendance', ['groupId' => $group->id]) }}" class="rounded-md bg-indigo-700 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Attendance</a>
                </div>
            </article>
        @empty
            <p class="py-10 text-sm text-gray-600">No groups are assigned to your leadership yet.</p>
        @endforelse
    </div>
</div>
