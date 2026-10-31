<section class="border-y border-gray-200 py-5">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase text-emerald-700">Group leadership</p>
            <h2 class="mt-1 text-lg font-bold text-gray-900">My Groups</h2>
        </div>
        <a href="{{ route('groups.lead.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">Open group dashboard</a>
    </div>
    <div class="mt-4 divide-y divide-gray-200">
        @foreach ($groups as $group)
            <a href="{{ route('groups.edit', $group) }}" class="grid gap-2 py-3 sm:grid-cols-[minmax(0,1fr)_repeat(3,auto)] sm:items-center" wire:key="overview-group-{{ $group->id }}">
                <span class="font-semibold text-gray-900">{{ $group->name }} <span class="font-normal text-gray-500">· {{ $group->members_count }} members</span></span>
                <span class="text-sm text-gray-600">{{ $group->last_meeting_on ? $group->last_meeting_attendance . ' present last meeting' : 'No attendance recorded' }}</span>
                <span class="text-sm text-amber-800">{{ $group->members_without_attendance }} without attendance</span>
                <span class="text-sm text-gray-600">{{ $group->open_serving_slots }} open slots</span>
            </a>
        @endforeach
    </div>
</section>