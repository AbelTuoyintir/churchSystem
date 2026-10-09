<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $isActive = '';
    public bool $confirmingTeamDeletion = false;
    public ?int $teamIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->teamIdBeingDeleted = $id;
        $this->confirmingTeamDeletion = true;
    }

    public function deleteTeam(): void
    {
        $team = Team::findOrFail($this->teamIdBeingDeleted);
        $this->authorize('delete', $team);

        $team->delete();

        $this->confirmingTeamDeletion = false;
        $this->teamIdBeingDeleted = null;

        session()->flash('success', 'Team deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Team::class);

        $teams = Team::query()
            ->with('leader')
            ->withCount(['members', 'positions'])
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->isActive !== '', function ($query) {
                $query->where('is_active', filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('name', 'asc')
            ->paginate(25);

        return view('livewire.teams.index', [
            'teams' => $teams,
        ])->layout('layouts.app', ['header' => 'Serving Teams']);
    }
}
