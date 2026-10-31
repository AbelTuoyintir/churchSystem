<?php

namespace App\Livewire\Schedule;

use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MySchedule extends Component
{
    public function confirm(int $assignmentId): void
    {
        $assignment = $this->ownAssignment($assignmentId);
        $assignment->update(['status' => 'confirmed']);
        session()->flash('success', 'Assignment confirmed.');
    }

    public function decline(int $assignmentId): void
    {
        $assignment = $this->ownAssignment($assignmentId);
        $assignment->update(['status' => 'declined']);
        session()->flash('success', 'Assignment declined.');
    }

    protected function ownAssignment(int $assignmentId): Assignment
    {
        $personId = Auth::user()?->person_id;
        abort_unless($personId, 403);

        return Assignment::query()
            ->where('person_id', $personId)
            ->findOrFail($assignmentId);
    }

    public function render()
    {
        $personId = Auth::user()?->person_id;

        $assignments = $personId
            ? Assignment::query()
                ->with(['team', 'position', 'service', 'event'])
                ->where('person_id', $personId)
                ->where('assigned_on', '>=', now()->toDateString())
                ->orderBy('assigned_on')
                ->get()
            : collect();

        return view('livewire.schedule.my-schedule', [
            'assignments' => $assignments,
        ])->layout('layouts.app', ['header' => 'My Schedule']);
    }
}
