<?php

namespace App\Livewire\Groups;

use App\Models\Attendance;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LeaderAttendance extends Component
{
    public string $groupId = '';

    public string $attendedOn = '';

    public array $statusByPerson = [];

    public function mount(?int $groupId = null): void
    {
        $user = auth()->user();
        abort_unless($user->isGroupLeader(), 403);
        $group = $groupId ? Group::query()->ledBy($user)->findOrFail($groupId) : Group::query()->ledBy($user)->orderBy('name')->first();
        $this->groupId = (string) ($group?->id ?? '');
        $this->attendedOn = now()->toDateString();
        $this->loadStatuses();
    }

    public function updatedGroupId(): void
    {
        $this->authorizedGroup();
        $this->loadStatuses();
    }

    public function updatedAttendedOn(): void
    {
        $this->validateOnly('attendedOn', ['attendedOn' => ['required', 'date']]);
        $this->loadStatuses();
    }

    public function markAllPresent(): void
    {
        $this->statusByPerson = $this->authorizedGroup()->members()->whereNull('left_at')
            ->pluck('person_id')->mapWithKeys(fn (int $personId): array => [$personId => 'present'])->all();
    }

    public function saveAttendance(): void
    {
        $group = $this->authorizedGroup();
        $validated = $this->validate([
            'attendedOn' => ['required', 'date'],
            'statusByPerson' => ['array'],
            'statusByPerson.*' => ['required', 'in:present,absent'],
        ]);
        $activePersonIds = $group->members()->whereNull('left_at')->pluck('person_id')->all();
        $submittedPersonIds = array_map('intval', array_keys($validated['statusByPerson']));
        abort_unless(
            array_diff($submittedPersonIds, $activePersonIds) === []
                && array_diff($activePersonIds, $submittedPersonIds) === [],
            403,
        );

        DB::transaction(function () use ($group, $validated): void {
            foreach ($validated['statusByPerson'] as $personId => $status) {
                Attendance::query()->updateOrCreate([
                    'group_id' => $group->id,
                    'person_id' => $personId,
                    'service_id' => null,
                    'event_id' => null,
                    'attended_on' => $validated['attendedOn'],
                ], ['status' => $status, 'recorded_by' => auth()->id()]);
            }
        });
        session()->flash('success', 'Group attendance saved.');
    }

    public function render()
    {
        $group = $this->groupId !== '' ? $this->authorizedGroup() : null;
        $members = $group
            ? $group->members()->with('person:id,first_name,last_name,email,phone')->whereNull('left_at')->orderBy('role')->get()
            : collect();
        $history = $group
            ? $group->attendances()->whereNull('service_id')->whereNull('event_id')->with('person')->latest('attended_on')->limit(30)->get()
            : collect();
        $attendanceTrend = [];

        if ($group) {
            for ($weeksAgo = 7; $weeksAgo >= 0; $weeksAgo--) {
                $weekStart = now()->startOfWeek()->subWeeks($weeksAgo);
                $weekEnd = $weekStart->copy()->endOfWeek();
                $attendanceTrend[] = [
                    'label' => $weekStart->format('M j'),
                    'count' => $group->attendances()
                        ->whereNull('service_id')
                        ->whereNull('event_id')
                        ->whereBetween('attended_on', [$weekStart->toDateString(), $weekEnd->toDateString()])
                        ->where('status', 'present')
                        ->count(),
                ];
            }
        }

        return view('livewire.groups.leader-attendance', [
            'groups' => Group::query()->ledBy(auth()->user())->orderBy('name')->get(),
            'group' => $group,
            'members' => $members,
            'history' => $history,
            'attendanceTrend' => $attendanceTrend,
            'attendanceTrendMax' => max(1, collect($attendanceTrend)->max('count') ?? 0),
        ])->layout('layouts.app', ['header' => 'Group Attendance']);
    }

    private function authorizedGroup(): Group
    {
        return Group::query()->ledBy(auth()->user())->findOrFail($this->groupId);
    }

    private function loadStatuses(): void
    {
        if ($this->groupId === '') {
            $this->statusByPerson = [];

            return;
        }
        $group = $this->authorizedGroup();
        $existing = Attendance::query()->where('group_id', $group->id)->whereNull('service_id')
            ->whereNull('event_id')->whereDate('attended_on', $this->attendedOn)->pluck('status', 'person_id');
        $this->statusByPerson = $group->members()->whereNull('left_at')->pluck('person_id')
            ->mapWithKeys(fn (int $personId): array => [$personId => $existing[$personId] ?? 'present'])->all();
    }
}
