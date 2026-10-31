<?php

namespace App\Livewire\Groups;

use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class LeadEvents extends Component
{
    use WithPagination;

    public string $groupId = '';

    public ?int $eventId = null;

    public string $title = '';

    public string $description = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public string $location = '';

    public string $category = '';

    public bool $allDay = false;

    public function mount(?int $groupId = null): void
    {
        $user = auth()->user();
        abort_unless($user->isGroupLeader(), 403);
        $this->groupId = (string) ($groupId && Group::query()->ledBy($user)->whereKey($groupId)->exists()
            ? $groupId
            : Group::query()->ledBy($user)->orderBy('name')->value('id'));
    }

    public function edit(int $eventId): void
    {
        $event = $this->ownedEvents()->findOrFail($eventId);
        $this->eventId = $event->id;
        $this->groupId = (string) $event->group_id;
        $this->title = $event->title;
        $this->description = $event->description ?? '';
        $this->startsAt = $event->starts_at->format('Y-m-d\TH:i');
        $this->endsAt = $event->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->location = $event->location ?? '';
        $this->category = $event->category ?? '';
        $this->allDay = $event->all_day;
    }

    public function save(): void
    {
        $group = Group::query()->ledBy(auth()->user())->findOrFail($this->groupId);
        $validated = $this->validate([
            'groupId' => ['required', 'integer', Rule::exists('groups', 'id')->where(fn ($query) => $query->whereIn('id', Group::query()->ledBy(auth()->user())->select('id')))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['nullable', 'date', 'after_or_equal:startsAt'],
            'location' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'allDay' => ['boolean'],
        ]);
        $event = $this->eventId ? $this->ownedEvents()->findOrFail($this->eventId) : new Event;
        $event->fill([
            'group_id' => $group->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?: null,
            'starts_at' => $validated['startsAt'],
            'ends_at' => $validated['endsAt'] ?: null,
            'location' => $validated['location'] ?: null,
            'category' => $validated['category'] ?: null,
            'all_day' => $validated['allDay'],
            'is_public' => false,
        ])->save();
        $this->resetForm();
        session()->flash('success', 'Group event saved.');
    }

    public function delete(int $eventId): void
    {
        $this->ownedEvents()->findOrFail($eventId)->delete();
        session()->flash('success', 'Group event deleted.');
    }

    public function render()
    {
        $groups = Group::query()->ledBy(auth()->user())->orderBy('name')->get();
        $events = $this->ownedEvents()
            ->when($this->groupId !== '', fn ($query) => $query->where('group_id', $this->groupId))
            ->with('group')
            ->orderByDesc('starts_at')
            ->paginate(12);

        return view('livewire.groups.lead-events', [
            'groups' => $groups,
            'events' => $events,
        ])->layout('layouts.app', ['header' => 'Group Events']);
    }

    private function ownedEvents(): Builder
    {
        return Event::query()->whereIn('group_id', Group::query()->ledBy(auth()->user())->select('id'));
    }

    public function resetForm(): void
    {
        $this->eventId = null;
        $this->title = '';
        $this->description = '';
        $this->startsAt = '';
        $this->endsAt = '';
        $this->location = '';
        $this->category = '';
        $this->allDay = false;
    }
}
