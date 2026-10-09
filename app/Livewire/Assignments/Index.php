<?php

namespace App\Livewire\Assignments;

use App\Models\Assignment;
use App\Models\Team;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $teamId = '';
    public string $status = '';
    public bool $confirmingAssignmentDeletion = false;
    public ?int $assignmentIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'teamId' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTeamId(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->assignmentIdBeingDeleted = $id;
        $this->confirmingAssignmentDeletion = true;
    }

    public function deleteAssignment(): void
    {
        $assignment = Assignment::findOrFail($this->assignmentIdBeingDeleted);
        $this->authorize('delete', $assignment);

        $assignment->delete();

        $this->confirmingAssignmentDeletion = false;
        $this->assignmentIdBeingDeleted = null;

        session()->flash('success', 'Serving assignment removed.');
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $assignment = Assignment::findOrFail($id);
        $this->authorize('update', $assignment);

        $assignment->update(['status' => $newStatus]);

        session()->flash('success', "Assignment status updated to {$newStatus}.");
    }

    public function render()
    {
        $this->authorize('viewAny', Assignment::class);

        $assignments = Assignment::query()
            ->with(['team', 'position', 'person', 'service', 'event'])
            ->when($this->search !== '', function ($query) {
                $query->whereHas('person', function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('team', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->teamId !== '', function ($query) {
                $query->where('team_id', $this->teamId);
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy('assigned_on', 'desc')
            ->paginate(25);

        $teams = Team::where('is_active', true)->orderBy('name')->get();

        return view('livewire.assignments.index', [
            'assignments' => $assignments,
            'teams' => $teams,
        ])->layout('layouts.app', ['header' => 'Serving Assignments']);
    }
}
