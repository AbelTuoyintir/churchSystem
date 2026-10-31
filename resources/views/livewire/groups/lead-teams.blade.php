<div class="mx-auto max-w-7xl space-y-6">
    <header class="border-b border-gray-200 pb-5"><p class="text-sm font-semibold uppercase text-emerald-700">Community</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Teams</h1></header>
    @if (session()->has('success'))<p class="border-l-4 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('success') }}</p>@endif
    <section><h2 class="text-base font-bold text-gray-900">Active teams</h2><div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">
        @forelse ($teams as $team)
            <div class="flex flex-wrap items-center justify-between gap-4 py-3" wire:key="team-{{ $team->id }}"><div><p class="font-semibold text-gray-900">{{ $team->name }}</p><p class="text-sm text-gray-600">{{ $team->members_count }} active members{{ $team->group ? ' · '.$team->group->name : '' }}</p></div>@if ((int) $team->leader_id === (int) auth()->user()->person_id)<button type="button" wire:click="$set('teamId', '{{ $team->id }}')" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-indigo-700">Manage schedule</button>@else<span class="text-xs font-semibold uppercase text-gray-500">Read only</span>@endif</div>
        @empty<p class="py-6 text-sm text-gray-600">No active teams.</p>@endforelse
    </div></section>
    @if ($selectedTeam)
        <section class="grid gap-8 lg:grid-cols-[minmax(18rem,0.8fr)_minmax(0,1.2fr)]">
            <form wire:submit="schedule" class="space-y-4 border-b border-gray-200 pb-6 lg:border-b-0 lg:border-r lg:pr-8"><h2 class="text-base font-bold text-gray-900">Schedule {{ $selectedTeam->name }}</h2>
                <label class="block text-sm font-medium text-gray-700">Position<select wire:model="positionId" class="mt-1 block w-full rounded-md border-gray-300 text-sm"><option value="">Choose position</option>@foreach ($selectedTeam->positions as $position)<option value="{{ $position->id }}">{{ $position->name }}</option>@endforeach</select></label>
                <label class="block text-sm font-medium text-gray-700">Team member<select wire:model="personId" class="mt-1 block w-full rounded-md border-gray-300 text-sm"><option value="">Choose member</option>@foreach ($selectedTeam->members->whereNull('left_at') as $member)<option value="{{ $member->person_id }}">{{ $member->person?->full_name }}</option>@endforeach</select></label>
                <label class="block text-sm font-medium text-gray-700">Date<input type="date" wire:model="assignedOn" class="mt-1 block w-full rounded-md border-gray-300 text-sm"></label>
                <button class="rounded-md bg-indigo-700 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-800">Save assignment</button>
            </form>
            <div><h2 class="text-base font-bold text-gray-900">Upcoming assignments</h2><div class="mt-3 divide-y divide-gray-200 border-y border-gray-200">@forelse ($assignments as $assignment)<div class="flex justify-between gap-4 py-3 text-sm" wire:key="assignment-{{ $assignment->id }}"><span class="font-medium text-gray-900">{{ $assignment->person?->full_name }} · {{ $assignment->position?->name }}</span><span class="text-gray-600">{{ $assignment->assigned_on->format('M j, Y') }}</span></div>@empty<p class="py-6 text-sm text-gray-600">No upcoming assignments.</p>@endforelse</div></div>
        </section>
    @endif
</div>