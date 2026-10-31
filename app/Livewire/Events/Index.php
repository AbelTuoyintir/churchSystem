<?php

namespace App\Livewire\Events;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function rsvp(int $eventId): void
    {
        $personId = Auth::user()?->person_id;
        abort_unless($personId, 403);

        $event = $this->visibleEvents($personId)->findOrFail($eventId);

        Attendance::query()->updateOrCreate(
            [
                'person_id' => $personId,
                'event_id' => $event->id,
            ],
            [
                'attended_on' => $event->starts_at->toDateString(),
                'status' => 'confirmed',
                'recorded_by' => Auth::id(),
            ]
        );

        session()->flash('success', 'You are registered for this event.');
    }

    public function cancelRsvp(int $eventId): void
    {
        $personId = Auth::user()?->person_id;
        abort_unless($personId, 403);

        $this->visibleEvents($personId)->findOrFail($eventId);

        Attendance::query()
            ->where('person_id', $personId)
            ->where('event_id', $eventId)
            ->delete();

        session()->flash('success', 'Your registration has been cancelled.');
    }

    public function render()
    {
        $personId = Auth::user()?->person_id;

        $events = $this->visibleEvents($personId)
            ->where('starts_at', '>=', now()->subDay())
            ->with('group')
            ->orderBy('starts_at')
            ->paginate(12);

        $registeredEventIds = $personId
            ? Attendance::query()
                ->where('person_id', $personId)
                ->whereNotNull('event_id')
                ->pluck('event_id')
            : collect();

        return view('livewire.events.index', [
            'events' => $events,
            'registeredEventIds' => $registeredEventIds,
        ])->layout('layouts.app', ['header' => 'Church Events']);
    }

    private function visibleEvents(?int $personId): Builder
    {
        if ($personId === null) {
            return Event::query()->where('is_public', true);
        }

        $visibleGroupIds = Group::query()
            ->where(function ($query) use ($personId): void {
                $query->where('leader_id', $personId)
                    ->orWhereHas('members', fn ($members) => $members
                        ->where('person_id', $personId)
                        ->whereNull('left_at'));
            })
            ->select('id');

        return Event::query()->where(function ($query) use ($visibleGroupIds): void {
            $query->where('is_public', true)
                ->orWhereIn('group_id', $visibleGroupIds);
        });
    }
}
