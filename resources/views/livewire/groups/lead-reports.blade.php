<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <p class="text-sm font-semibold uppercase text-emerald-700">Leadership</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Group Reports</h1>
        </div>
        <label class="text-sm font-medium text-gray-700">Group
            <select wire:model.live="groupId" class="mt-1 block min-w-56 rounded-md border-gray-300 text-sm">
                @foreach ($groups as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach
            </select>
        </label>
    </header>

    @if ($group)
        <section class="border-b border-gray-200 pb-6">
            <h2 class="text-base font-bold text-gray-900">Attendance trend</h2>
            <div class="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-8" role="img" aria-label="Weekly attendance counts for {{ $group->name }}">
                @foreach ($weeklyAttendance as $week)
                    <div class="flex min-w-0 flex-col justify-end gap-2 text-center" wire:key="attendance-week-{{ $loop->index }}">
                        <span class="text-xs font-semibold text-gray-700">{{ $week['count'] }}</span>
                        <div class="flex h-24 items-end border-b border-gray-200"><div class="w-full bg-emerald-600" style="height: {{ max(4, (int) round($week['count'] / $attendanceMax * 100)) }}%"></div></div>
                        <span class="truncate text-xs text-gray-500">{{ $week['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="grid gap-8 lg:grid-cols-[minmax(0,1.3fr)_minmax(18rem,1fr)]">
            <div>
                <h2 class="text-base font-bold text-gray-900">Roster engagement</h2>
                <p class="mt-1 text-sm text-gray-600">Active members with no recorded attendance in the past 30 days.</p>
                <div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">
                    @forelse ($driftingMembers as $member)
                        <div class="flex items-center justify-between gap-4 py-3" wire:key="drifting-{{ $member->id }}">
                            <span class="text-sm font-medium text-gray-900">{{ $member->person?->full_name ?? 'Former member record' }}</span>
                            <span class="text-xs text-gray-500">Joined {{ $member->joined_at ? \Illuminate\Support\Carbon::parse($member->joined_at)->format('M j, Y') : 'date unknown' }}</span>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-gray-600">Everyone on the active roster has recent attendance.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="text-base font-bold text-gray-900">Roster movement</h2>
                <p class="mt-1 text-sm text-gray-600">Members joining and leaving by month.</p>
                <div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">
                    @foreach ($joinLeaveHistory as $month)
                        <div class="grid grid-cols-3 gap-3 py-3 text-sm" wire:key="roster-month-{{ $loop->index }}">
                            <span class="font-medium text-gray-700">{{ $month['label'] }}</span>
                            <span class="text-emerald-800">+{{ $month['joined'] }} joined</span>
                            <span class="text-rose-800">-{{ $month['left'] }} left</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <p class="py-10 text-sm text-gray-600">No groups are assigned to your leadership yet.</p>
    @endif
</div>
