<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use Livewire\Component;

class LeadIndex extends Component
{
    public function render()
    {
        $user = auth()->user();
        abort_unless($user->isGroupLeader(), 403);

        $groups = Group::query()
            ->ledBy($user)
            ->withCount(['members' => fn ($query) => $query->whereNull('left_at')])
            ->with(['teams.positions' => fn ($query) => $query->withCount([
                'members' => fn ($members) => $members->whereNull('left_at'),
            ])])
            ->orderBy('name')
            ->get();

        foreach ($groups as $group) {
            $meetings = $group->attendances()->whereNull('service_id')->whereNull('event_id');
            $lastMeeting = (clone $meetings)->max('attended_on');
            $group->setAttribute('last_meeting_on', $lastMeeting);
            $group->setAttribute('last_meeting_attendance', $lastMeeting
                ? (clone $meetings)->whereDate('attended_on', $lastMeeting)->where('status', 'present')->count()
                : 0);
            $group->setAttribute('members_without_attendance', $group->members()
                ->whereNull('left_at')
                ->whereDoesntHave('person.attendances', fn ($query) => $query
                    ->where('group_id', $group->id)
                    ->whereNull('service_id')
                    ->whereNull('event_id')
                    ->where('status', 'present')
                    ->where('attended_on', '>=', now()->subDays(30)->toDateString()))
                ->count());

            $openSlots = $group->teams->flatMap->positions->sum(fn ($position) => max(
                0,
                $position->slots_per_service - $position->members_count,
            ));
            $group->setAttribute('open_serving_slots', $openSlots);
        }

        return view('livewire.groups.lead-index', [
            'groups' => $groups,
        ])->layout('layouts.app', ['header' => 'My Groups']);
    }
}
