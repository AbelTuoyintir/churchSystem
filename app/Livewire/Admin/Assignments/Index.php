<?php

namespace App\Livewire\Admin\Assignments;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Person;
use App\Models\Service;
use App\Models\Team;
use App\Models\TeamPosition;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingAssignmentId = null;

    public ?int $teamId = null;

    public ?int $positionId = null;

    public ?int $personId = null;

    public ?int $serviceId = null;

    public ?int $eventId = null;

    public string $assignedOn = '';

    public string $status = 'scheduled';

    public string $notes = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Assignment::class);
        $this->assignedOn = today()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $assignmentId): void
    {
        $assignment = Assignment::query()->findOrFail($assignmentId);
        $this->authorize('update', $assignment);

        $this->editingAssignmentId = $assignment->id;
        $this->teamId = $assignment->team_id;
        $this->positionId = $assignment->position_id;
        $this->personId = $assignment->person_id;
        $this->serviceId = $assignment->service_id;
        $this->eventId = $assignment->event_id;
        $this->assignedOn = $assignment->assigned_on?->toDateString() ?? today()->toDateString();
        $this->status = $assignment->status;
        $this->notes = $assignment->notes ?? '';
    }

    public function save(): void
    {
        $assignment = $this->editingAssignmentId
            ? Assignment::query()->findOrFail($this->editingAssignmentId)
            : new Assignment;
        $this->authorize($this->editingAssignmentId ? 'update' : 'create', $assignment);

        $validated = $this->validate([
            'teamId' => ['required', 'integer', 'exists:teams,id'],
            'positionId' => ['nullable', 'integer', 'exists:team_positions,id'],
            'personId' => ['required', 'integer', 'exists:people,id'],
            'serviceId' => ['nullable', 'integer', 'exists:services,id'],
            'eventId' => ['nullable', 'integer', 'exists:events,id'],
            'assignedOn' => ['required', 'date'],
            'status' => ['required', 'in:scheduled,confirmed,declined,completed'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $assignment->fill([
            'team_id' => $validated['teamId'],
            'position_id' => $validated['positionId'] ?? null,
            'person_id' => $validated['personId'],
            'service_id' => $validated['serviceId'] ?? null,
            'event_id' => $validated['eventId'] ?? null,
            'assigned_on' => $validated['assignedOn'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ])->save();

        $this->resetForm();
        $this->dispatch('notify', type: 'success', message: 'Assignment saved.');
    }

    public function delete(int $assignmentId): void
    {
        $assignment = Assignment::query()->findOrFail($assignmentId);
        $this->authorize('delete', $assignment);
        $assignment->delete();
        $this->dispatch('notify', type: 'success', message: 'Assignment deleted.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingAssignmentId', 'teamId', 'positionId', 'personId', 'serviceId', 'eventId', 'notes']);
        $this->assignedOn = today()->toDateString();
        $this->status = 'scheduled';
    }

    public function render()
    {
        $this->authorize('viewAny', Assignment::class);

        $assignments = Assignment::query()
            ->with(['team', 'position', 'person', 'service', 'event'])
            ->when($this->search !== '', fn ($query) => $query->whereHas('person', fn ($people) => $people
                ->where('first_name', 'like', '%'.$this->search.'%')
                ->orWhere('last_name', 'like', '%'.$this->search.'%'))
                ->orWhereHas('team', fn ($teams) => $teams->where('name', 'like', '%'.$this->search.'%')))
            ->latest('assigned_on')
            ->paginate(15);

        return view('livewire.admin.assignments.index', [
            'assignments' => $assignments,
            'teams' => Team::query()->orderBy('name')->get(),
            'positions' => $this->teamId ? TeamPosition::query()->where('team_id', $this->teamId)->orderBy('name')->get() : collect(),
            'people' => Person::query()->where('is_active', true)->orderBy('last_name')->orderBy('first_name')->get(),
            'services' => Service::query()->where('is_active', true)->orderBy('name')->get(),
            'events' => Event::query()->where('starts_at', '>=', now()->subMonth())->orderBy('starts_at')->get(),
        ])->layout('layouts.app', ['header' => 'Scheduling']);
    }
}
