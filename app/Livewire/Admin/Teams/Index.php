<?php

namespace App\Livewire\Admin\Teams;

use App\Models\Group;
use App\Models\Person;
use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingTeamId = null;

    public string $name = '';

    public string $description = '';

    public ?int $leaderId = null;

    public ?int $groupId = null;

    public bool $isActive = true;

    public function mount(): void
    {
        $this->authorize('viewAny', Team::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $teamId): void
    {
        $team = Team::query()->findOrFail($teamId);
        $this->authorize('update', $team);
        $this->editingTeamId = $team->id;
        $this->name = $team->name;
        $this->description = $team->description ?? '';
        $this->leaderId = $team->leader_id;
        $this->groupId = $team->group_id;
        $this->isActive = $team->is_active;
    }

    public function save(): void
    {
        $team = $this->editingTeamId ? Team::query()->findOrFail($this->editingTeamId) : new Team;
        $this->authorize($this->editingTeamId ? 'update' : 'create', $team);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'leaderId' => ['nullable', 'integer', 'exists:people,id'],
            'groupId' => ['nullable', 'integer', 'exists:groups,id'],
            'isActive' => ['boolean'],
        ]);

        $team->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'leader_id' => $validated['leaderId'] ?? null,
            'group_id' => $validated['groupId'] ?? null,
            'is_active' => $validated['isActive'],
        ])->save();

        $this->resetForm();
        $this->dispatch('notify', type: 'success', message: 'Team saved.');
    }

    public function delete(int $teamId): void
    {
        $team = Team::query()->findOrFail($teamId);
        $this->authorize('delete', $team);
        $team->delete();
        $this->dispatch('notify', type: 'success', message: 'Team deleted.');
    }

    public function resetForm(): void
    {
        $this->authorize('viewAny', Team::class);
        $this->reset(['editingTeamId', 'name', 'description', 'leaderId', 'groupId']);
        $this->isActive = true;
    }

    public function render()
    {
        $this->authorize('viewAny', Team::class);

        return view('livewire.admin.teams.index', [
            'teams' => Team::query()
                ->with(['leader', 'group'])
                ->withCount('members')
                ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->latest()
                ->paginate(15),
            'people' => Person::query()->where('is_active', true)->orderBy('last_name')->orderBy('first_name')->get(),
            'groups' => Group::query()->orderBy('name')->get(),
        ])->layout('layouts.app', ['header' => 'Teams']);
    }
}
