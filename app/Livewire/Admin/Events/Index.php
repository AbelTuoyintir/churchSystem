<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingEventId = null;

    public string $title = '';

    public string $description = '';

    public string $startsAt = '';

    public string $endsAt = '';

    public bool $allDay = false;

    public string $location = '';

    public string $category = '';

    public bool $isPublic = true;

    public ?int $groupId = null;

    public function mount(): void
    {
        $this->authorizeStaff();
        $this->startsAt = now()->format('Y-m-d\TH:i');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $eventId): void
    {
        $event = Event::query()->findOrFail($eventId);
        $this->authorize('update', $event);
        $this->editingEventId = $event->id;
        $this->title = $event->title;
        $this->description = $event->description ?? '';
        $this->startsAt = $event->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->endsAt = $event->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->allDay = $event->all_day;
        $this->location = $event->location ?? '';
        $this->category = $event->category ?? '';
        $this->isPublic = $event->is_public;
        $this->groupId = $event->group_id;
    }

    public function save(): void
    {
        $event = $this->editingEventId ? Event::query()->findOrFail($this->editingEventId) : new Event;
        $this->authorize($this->editingEventId ? 'update' : 'create', $event);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'startsAt' => ['required', 'date'],
            'endsAt' => ['nullable', 'date', 'after_or_equal:startsAt'],
            'allDay' => ['boolean'],
            'location' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'isPublic' => ['boolean'],
            'groupId' => ['nullable', 'integer', 'exists:groups,id'],
        ]);

        $event->fill([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['startsAt'],
            'ends_at' => $validated['endsAt'] ?? null,
            'all_day' => $validated['allDay'],
            'location' => $validated['location'] ?? null,
            'category' => $validated['category'] ?? null,
            'is_public' => $validated['isPublic'],
            'group_id' => $validated['groupId'] ?? null,
        ])->save();

        $this->resetForm();
        $this->dispatch('notify', type: 'success', message: 'Event saved.');
    }

    public function delete(int $eventId): void
    {
        $event = Event::query()->findOrFail($eventId);
        $this->authorize('delete', $event);
        $event->delete();
        $this->dispatch('notify', type: 'success', message: 'Event deleted.');
    }

    public function resetForm(): void
    {
        $this->authorizeStaff();
        $this->reset(['editingEventId', 'title', 'description', 'endsAt', 'location', 'category', 'groupId']);
        $this->startsAt = now()->format('Y-m-d\TH:i');
        $this->allDay = false;
        $this->isPublic = true;
    }

    public function render()
    {
        $this->authorizeStaff();

        return view('livewire.admin.events.index', [
            'events' => Event::query()
                ->with('group')
                ->when($this->search !== '', fn ($query) => $query
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('location', 'like', '%'.$this->search.'%'))
                ->latest('starts_at')
                ->paginate(15),
            'groups' => Group::query()->orderBy('name')->get(),
        ])->layout('layouts.app', ['header' => 'Events']);
    }

    private function authorizeStaff(): void
    {
        abort_unless(auth()->user()?->isStaff(), 403);
    }
}
