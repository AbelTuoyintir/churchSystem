<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <p class="text-sm font-semibold uppercase text-emerald-700">Community</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Find a Group</h1>
            <p class="mt-1 text-sm text-gray-600">Explore active groups and request to join an open group.</p>
        </div>
        <a href="{{ route('members.portal') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">My profile</a>
    </header>

    @if (session()->has('success'))
        <div role="status" class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</div>
    @endif

    <div class="grid gap-3 border-b border-gray-200 pb-5 sm:grid-cols-[minmax(0,1fr)_14rem]">
        <div>
            <label for="group-search" class="sr-only">Search groups</label>
            <input id="group-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search groups" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
        </div>
        <div>
            <label for="group-type" class="sr-only">Filter by group type</label>
            <select id="group-type" wire:model.live="type" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-600 focus:ring-indigo-600">
                <option value="">All types</option>
                <option value="small_group">Small group</option>
                <option value="ministry">Ministry</option>
                <option value="committee">Committee</option>
                <option value="class">Class</option>
            </select>
        </div>
    </div>

    <div class="grid gap-x-8 gap-y-7 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($groups as $group)
            @php
                $isMember = $memberGroupIds->contains($group->id);
                $hasPendingRequest = $pendingGroupIds->contains($group->id);
            @endphp
            <article wire:key="group-{{ $group->id }}" class="flex min-h-56 flex-col border-b border-gray-200 pb-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase text-gray-500">{{ str_replace('_', ' ', $group->type) }}</p>
                        <h2 class="mt-1 text-lg font-bold text-gray-900">{{ $group->name }}</h2>
                    </div>
                    <span @class([
                        'shrink-0 rounded px-2 py-1 text-xs font-semibold',
                        'bg-emerald-50 text-emerald-800' => $group->is_open,
                        'bg-gray-100 text-gray-700' => ! $group->is_open,
                    ])>{{ $group->is_open ? 'Open' : 'Closed' }}</span>
                </div>
                @if ($group->description)
                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-600">{{ $group->description }}</p>
                @endif
                <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Leader</dt>
                        <dd class="mt-0.5 text-gray-900">{{ $group->leader?->full_name ?? 'Not listed' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-gray-500">Meeting</dt>
                        <dd class="mt-0.5 text-gray-900">{{ implode(' ', array_filter([$group->meeting_day, $group->meeting_time ? \Illuminate\Support\Carbon::parse($group->meeting_time)->format('g:i A') : null])) ?: 'To be announced' }}</dd>
                    </div>
                </dl>
                <div class="mt-auto flex flex-wrap items-center gap-3 pt-5">
                    @if ($isMember)
                        <a href="{{ route('members.groups.show', $group) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">View group</a>
                        <span class="text-xs font-semibold uppercase text-emerald-800">Your group</span>
                    @elseif ($hasPendingRequest)
                        <span class="text-sm font-semibold text-gray-600">Request pending</span>
                    @elseif ($group->is_open && auth()->user()?->person_id)
                        <button type="button" wire:click="requestToJoin({{ $group->id }})" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-700">Request to join</button>
                    @elseif ($group->is_open)
                        <span class="text-sm text-gray-500">Link a member profile to request access.</span>
                    @else
                        <span class="text-sm text-gray-500">Not accepting requests</span>
                    @endif
                </div>
            </article>
        @empty
            <p class="py-10 text-sm text-gray-600">No active groups match your search.</p>
        @endforelse
    </div>

    <div>{{ $groups->links() }}</div>
</div>