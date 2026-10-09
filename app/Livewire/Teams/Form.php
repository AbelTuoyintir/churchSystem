<?php

namespace App\Livewire\Teams;

use App\Models\Person;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamPosition;
use Livewire\Component;

class Form extends Component
{
    public ?Team $team = null;

    public string $name = '';
    public string $description = '';
    public ?int $leader_id = null;
    public bool $is_active = true;

    // Position creation fields
    public string $positionName = '';
    public string $positionDescription = '';
    public int $slotsPerService = 1;

    // Member attachment fields
    public ?int $selectedPersonId = null;
    public ?int $selectedPositionId = null;
    public string $joinedAt = '';

    public bool $confirmingPositionDelete = false;
    public ?int $positionIdToDelete = null;

    public bool $confirmingMemberDetach = false;
    public ?int $teamMemberIdToDetach = null;

    public function mount(?Team $team = null): void
    {
        $this->joinedAt = now()->format('Y-m-d');

        if ($team && $team->exists) {
            $this->authorize('view', $team);
            $this->team = $team;

            $this->name = $team->name ?? '';
            $this->description = $team->description ?? '';
            $this->leader_id = $team->leader_id;
            $this->is_active = (bool) ($team->is_active ?? true);
        } else {
            $this->authorize('create', Team::class);
            $this->team = new Team();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:people,id',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->team && $this->team->exists) {
            $this->authorize('update', $this->team);
        } else {
            $this->authorize('create', Team::class);
        }

        $validated = $this->validate();
        if (empty($validated['leader_id'])) {
            $validated['leader_id'] = null;
        }

        if ($this->team && $this->team->exists) {
            $this->team->update($validated);
            session()->flash('success', 'Team updated successfully.');
        } else {
            $this->team = Team::create($validated);
            session()->flash('success', 'Team created successfully.');
        }

        return redirect()->route('teams.index');
    }

    public function addPosition(): void
    {
        if (!$this->team || !$this->team->exists) {
            return;
        }

        $this->authorize('update', $this->team);

        $this->validate([
            'positionName' => 'required|string|max:255',
            'positionDescription' => 'nullable|string',
            'slotsPerService' => 'required|integer|min:1',
        ]);

        TeamPosition::create([
            'team_id' => $this->team->id,
            'name' => $this->positionName,
            'description' => $this->positionDescription,
            'slots_per_service' => $this->slotsPerService,
        ]);

        $this->reset(['positionName', 'positionDescription']);
        $this->slotsPerService = 1;

        session()->flash('position_success', 'Team position added successfully.');
    }

    public function confirmDeletePosition(int $id): void
    {
        $this->positionIdToDelete = $id;
        $this->confirmingPositionDelete = true;
    }

    public function deletePosition(): void
    {
        if (!$this->team || !$this->team->exists) {
            return;
        }

        $this->authorize('update', $this->team);

        if ($this->positionIdToDelete) {
            TeamPosition::where('team_id', $this->team->id)
                ->where('id', $this->positionIdToDelete)
                ->delete();
        }

        $this->confirmingPositionDelete = false;
        $this->positionIdToDelete = null;

        session()->flash('position_success', 'Team position removed.');
    }

    public function attachMember(): void
    {
        if (!$this->team || !$this->team->exists) {
            return;
        }

        $this->authorize('update', $this->team);

        $this->validate([
            'selectedPersonId' => 'required|exists:people,id',
            'selectedPositionId' => 'nullable|exists:team_positions,id',
            'joinedAt' => 'required|date',
        ]);

        TeamMember::create([
            'team_id' => $this->team->id,
            'person_id' => $this->selectedPersonId,
            'position_id' => $this->selectedPositionId ?: null,
            'joined_at' => $this->joinedAt,
        ]);

        $this->reset(['selectedPersonId', 'selectedPositionId']);
        $this->joinedAt = now()->format('Y-m-d');

        session()->flash('member_success', 'Team member attached successfully.');
    }

    public function confirmDetachMember(int $id): void
    {
        $this->teamMemberIdToDetach = $id;
        $this->confirmingMemberDetach = true;
    }

    public function detachMember(): void
    {
        if (!$this->team || !$this->team->exists) {
            return;
        }

        $this->authorize('update', $this->team);

        if ($this->teamMemberIdToDetach) {
            $memberRecord = TeamMember::where('team_id', $this->team->id)
                ->where('id', $this->teamMemberIdToDetach)
                ->first();

            if ($memberRecord) {
                $memberRecord->update(['left_at' => now()->toDateString()]);
            }
        }

        $this->confirmingMemberDetach = false;
        $this->teamMemberIdToDetach = null;

        session()->flash('member_success', 'Team member marked as left.');
    }

    public function render()
    {
        $allPeople = Person::query()->orderBy('last_name')->orderBy('first_name')->get();

        $positions = ($this->team && $this->team->exists)
            ? TeamPosition::where('team_id', $this->team->id)->get()
            : collect();

        $currentMembers = ($this->team && $this->team->exists)
            ? TeamMember::with(['person', 'position'])
                ->where('team_id', $this->team->id)
                ->whereNull('left_at')
                ->get()
            : collect();

        return view('livewire.teams.form', [
            'allPeople' => $allPeople,
            'positions' => $positions,
            'currentMembers' => $currentMembers,
        ])->layout('layouts.app', ['header' => $this->team && $this->team->exists ? 'Edit Team' : 'Create Team']);
    }
}
