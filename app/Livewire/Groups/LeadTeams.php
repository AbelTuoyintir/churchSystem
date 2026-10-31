<?php

namespace App\Livewire\Groups;

use App\Models\Assignment;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Component;

class LeadTeams extends Component
{
    public string $teamId = '';

    public ?int $positionId = null;

    public ?int $personId = null;

    public string $assignedOn = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isGroupLeader(), 403);
        $this->assignedOn = now()->toDateString();
    }

    public function schedule(): void
    {
        $team = $this->ledTeams()->findOrFail($this->teamId);
        $validated = $this->validate([
            'teamId' => ['required', 'integer'],
            'positionId' => ['required', 'integer', Rule::exists('team_positions', 'id')->where('team_id', $team->id)],
            'personId' => ['required', 'integer', Rule::exists('team_members', 'person_id')->where('team_id', $team->id)->whereNull('left_at')],
            'assignedOn' => ['required', 'date'],
        ]);

        Assignment::query()->create([
            'team_id' => $team->id,
            'position_id' => $validated['positionId'],
            'person_id' => $validated['personId'],
            'assigned_on' => $validated['assignedOn'],
            'status' => 'assigned',
        ]);
        session()->flash('success', 'Team assignment scheduled.');
    }

    public function render()
    {
        $teams = Team::query()->where('is_active', true)->with(['leader', 'group'])
            ->withCount(['members' => fn ($query) => $query->whereNull('left_at')])
            ->orderBy('name')->get();
        $selectedTeam = $this->teamId !== '' ? $this->ledTeams()->with(['positions', 'members.person'])->find($this->teamId) : null;
        $assignments = $selectedTeam?->assignments()->with(['person', 'position'])->whereDate('assigned_on', '>=', now()->toDateString())->orderBy('assigned_on')->get() ?? collect();

        return view('livewire.groups.lead-teams', [
            'teams' => $teams,
            'selectedTeam' => $selectedTeam,
            'assignments' => $assignments,
        ])->layout('layouts.app', ['header' => 'Teams']);
    }

    private function ledTeams(): Builder
    {
        return Team::query()->where('leader_id', auth()->user()->person_id);
    }
}
