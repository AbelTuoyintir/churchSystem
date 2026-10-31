<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\GroupMember;
use Livewire\Component;

class LeadReports extends Component
{
    public string $groupId = '';

    public function mount(?int $groupId = null): void
    {
        $user = auth()->user();
        abort_unless($user->isGroupLeader(), 403);
        $this->groupId = (string) ($groupId && Group::query()->ledBy($user)->whereKey($groupId)->exists()
            ? $groupId
            : Group::query()->ledBy($user)->orderBy('name')->value('id'));
    }

    public function render()
    {
        $user = auth()->user();
        $groups = Group::query()->ledBy($user)->orderBy('name')->get();
        $group = $this->groupId !== ''
            ? Group::query()->ledBy($user)->findOrFail($this->groupId)
            : null;

        $weeklyAttendance = [];
        $joinLeaveHistory = [];
        $driftingMembers = collect();

        if ($group) {
            for ($weeksAgo = 7; $weeksAgo >= 0; $weeksAgo--) {
                $weekStart = now()->startOfWeek()->subWeeks($weeksAgo);
                $weekEnd = $weekStart->copy()->endOfWeek();
                $weeklyAttendance[] = [
                    'label' => $weekStart->format('M j'),
                    'count' => $group->attendances()
                        ->whereNull('service_id')
                        ->whereNull('event_id')
                        ->whereBetween('attended_on', [$weekStart->toDateString(), $weekEnd->toDateString()])
                        ->where('status', 'present')
                        ->count(),
                ];
            }

            for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
                $month = now()->startOfMonth()->subMonths($monthsAgo);
                $nextMonth = $month->copy()->addMonth();
                $joinLeaveHistory[] = [
                    'label' => $month->format('M Y'),
                    'joined' => $group->members()->whereBetween('joined_at', [$month->toDateString(), $nextMonth->copy()->subDay()->toDateString()])->count(),
                    'left' => $group->members()->whereBetween('left_at', [$month->toDateString(), $nextMonth->copy()->subDay()->toDateString()])->count(),
                ];
            }

            $driftingMembers = GroupMember::query()
                ->with('person:id,first_name,last_name,email,phone')
                ->where('group_id', $group->id)
                ->whereNull('left_at')
                ->whereDoesntHave('person.attendances', fn ($query) => $query
                    ->where('group_id', $group->id)
                    ->whereNull('service_id')
                    ->whereNull('event_id')
                    ->where('status', 'present')
                    ->where('attended_on', '>=', now()->subDays(30)->toDateString()))
                ->orderBy('joined_at')
                ->get();
        }

        $attendanceMax = max(1, collect($weeklyAttendance)->max('count') ?? 0);

        return view('livewire.groups.lead-reports', [
            'groups' => $groups,
            'group' => $group,
            'weeklyAttendance' => $weeklyAttendance,
            'joinLeaveHistory' => $joinLeaveHistory,
            'driftingMembers' => $driftingMembers,
            'attendanceMax' => $attendanceMax,
        ])->layout('layouts.app', ['header' => 'Group Reports']);
    }
}
