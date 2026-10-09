<?php

namespace App\Livewire\Assignments;

use App\Models\Assignment;
use App\Models\Event;
use App\Models\Person;
use App\Models\Service;
use App\Models\Team;
use App\Models\TeamPosition;
use Livewire\Component;

class Form extends Component
{
    public ?Assignment $assignment = null;

    public ?int $team_id = null;
    public ?int $position_id = null;
    public ?int $person_id = null;
    public ?int $service_id = null;
    public ?int $event_id = null;
    public string $assigned_on = '';
    public string $status = 'assigned';
    public string $notes = '';

    public function mount(?Assignment $assignment = null): void
    {
        $this->assigned_on = now()->format('Y-m-d');

        if ($assignment && $assignment->exists) {
            $this->authorize('view', $assignment);
            $this->assignment = $assignment;

            $this->team_id = $assignment->team_id;
            $this->position_id = $assignment->position_id;
            $this->person_id = $assignment->person_id;
            $this->service_id = $assignment->service_id;
            $this->event_id = $assignment->event_id;
            $this->assigned_on = $assignment->assigned_on ? $assignment->assigned_on->format('Y-m-d') : now()->format('Y-m-d');
            $this->status = $assignment->status ?? 'assigned';
            $this->notes = $assignment->notes ?? '';
        } else {
            $this->authorize('create', Assignment::class);
            $this->assignment = new Assignment();
        }
    }

    protected function rules(): array
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'position_id' => 'nullable|exists:team_positions,id',
            'person_id' => 'required|exists:people,id',
            'service_id' => 'nullable|exists:services,id',
            'event_id' => 'nullable|exists:events,id',
            'assigned_on' => 'required|date',
            'status' => 'required|string|in:assigned,confirmed,declined',
            'notes' => 'nullable|string',
        ];
    }

    public function save()
    {
        if ($this->assignment && $this->assignment->exists) {
            $this->authorize('update', $this->assignment);
        } else {
            $this->authorize('create', Assignment::class);
        }

        $validated = $this->validate();

        if (empty($validated['position_id'])) {
            $validated['position_id'] = null;
        }
        if (empty($validated['service_id'])) {
            $validated['service_id'] = null;
        }
        if (empty($validated['event_id'])) {
            $validated['event_id'] = null;
        }

        if ($this->assignment && $this->assignment->exists) {
            $this->assignment->update($validated);
            session()->flash('success', 'Serving assignment updated successfully.');
        } else {
            $this->assignment = Assignment::create($validated);
            session()->flash('success', 'Serving assignment created successfully.');
        }

        return redirect()->route('assignments.index');
    }

    public function render()
    {
        $teams = Team::where('is_active', true)->orderBy('name')->get();

        $positions = $this->team_id
            ? TeamPosition::where('team_id', $this->team_id)->orderBy('name')->get()
            : collect();

        $people = Person::orderBy('last_name')->orderBy('first_name')->get();
        $services = Service::where('is_active', true)->orderBy('starts_at', 'desc')->get();
        $events = Event::orderBy('starts_at', 'desc')->get();

        return view('livewire.assignments.form', [
            'teams' => $teams,
            'positions' => $positions,
            'people' => $people,
            'services' => $services,
            'events' => $events,
        ])->layout('layouts.app', [
            'header' => $this->assignment && $this->assignment->exists ? 'Edit Assignment' : 'Create Assignment',
        ]);
    }
}
