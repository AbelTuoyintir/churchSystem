<div>
<div class="mx-auto max-w-7xl space-y-6">
    <header class="flex flex-wrap items-end justify-between gap-4 border-b border-gray-200 pb-5">
        <div><p class="text-sm font-semibold uppercase text-emerald-700">Leadership</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Group Attendance</h1></div>
        <div class="flex flex-wrap gap-3">
            <label class="text-sm font-medium text-gray-700">Group
                <select wire:model.live="groupId" class="mt-1 block min-w-48 rounded-md border-gray-300 text-sm">
                    @foreach ($groups as $option)<option value="{{ $option->id }}">{{ $option->name }}</option>@endforeach
                </select>
            </label>
            <label class="text-sm font-medium text-gray-700">Meeting date
                <input type="date" wire:model.live="attendedOn" class="mt-1 block rounded-md border-gray-300 text-sm">
            </label>
        </div>
    </header>

    @if (session()->has('success'))<p class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</p>@endif

    @if ($group)
        <section class="border-b border-gray-200 pb-5">
            <h2 class="text-base font-bold text-gray-900">Weekly attendance trend</h2>
            <div class="mt-3 grid grid-cols-4 gap-3 sm:grid-cols-8" role="img" aria-label="Weekly attendance counts for {{ $group->name }}">
                @foreach ($attendanceTrend as $week)
                    <div class="flex min-w-0 flex-col justify-end gap-2 text-center" wire:key="meeting-trend-{{ $loop->index }}">
                        <span class="text-xs font-semibold text-gray-700">{{ $week['count'] }}</span>
                        <div class="flex h-20 items-end border-b border-gray-200"><div class="w-full bg-emerald-600" style="height: {{ max(4, (int) round($week['count'] / $attendanceTrendMax * 100)) }}%"></div></div>
                        <span class="truncate text-xs text-gray-500">{{ $week['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><h2 class="text-lg font-bold text-gray-900">{{ $group->name }}</h2><p class="text-sm text-gray-600">{{ $members->count() }} active roster members</p></div>
                <div class="flex gap-2">
                    <button type="button" wire:click="markAllPresent" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Mark all present</button>
                    <button type="button" wire:click="saveAttendance" wire:loading.attr="disabled" class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Save attendance</button>
                </div>
            </div>

            <div class="mt-4 divide-y divide-gray-200 border-y border-gray-200">
                @forelse ($members as $member)
                    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-3" wire:key="attendance-person-{{ $member->person_id }}">
                        <div><p class="text-sm font-semibold text-gray-900">{{ $member->person?->full_name ?? 'Unknown person' }}</p><p class="text-xs text-gray-500">{{ $member->person?->email ?: $member->person?->phone ?: 'No contact listed' }}</p></div>
                        <select aria-label="Attendance for {{ $member->person?->full_name }}" wire:model="statusByPerson.{{ $member->person_id }}" class="rounded-md border-gray-300 text-sm">
                            <option value="present">Present</option><option value="absent">Absent</option>
                        </select>
                    </div>
                @empty
                    <p class="py-8 text-sm text-gray-600">This group has no active members.</p>
                @endforelse
            </div>
            @error('statusByPerson.*')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </section>

        <section>
            <h2 class="text-base font-bold text-gray-900">Recent group meetings</h2>
            <div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">
                @forelse ($history->groupBy(fn ($attendance) => $attendance->attended_on->toDateString()) as $date => $records)
                    <div class="flex justify-between gap-3 py-3 text-sm" wire:key="meeting-{{ $date }}"><span class="font-medium text-gray-800">{{ \Illuminate\Support\Carbon::parse($date)->format('D, M j, Y') }}</span><span class="text-gray-600">{{ $records->where('status', 'present')->count() }} present · {{ $records->where('status', 'absent')->count() }} absent</span></div>
                @empty
                    <p class="py-5 text-sm text-gray-600">No group attendance has been recorded.</p>
                @endforelse
            </div>
        </section>
    @else
        <p class="py-10 text-sm text-gray-600">No groups are assigned to your leadership yet.</p>
    @endif
</div>
</div>
