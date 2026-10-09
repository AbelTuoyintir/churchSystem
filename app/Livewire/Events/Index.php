<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public string $isPublic = '';
    public bool $confirmingEventDeletion = false;
    public ?int $eventIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'isPublic' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingIsPublic(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->eventIdBeingDeleted = $id;
        $this->confirmingEventDeletion = true;
    }

    public function deleteEvent(): void
    {
        $event = Event::findOrFail($this->eventIdBeingDeleted);
        $this->authorize('delete', $event);

        $event->delete();

        $this->confirmingEventDeletion = false;
        $this->eventIdBeingDeleted = null;

        session()->flash('success', 'Event deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category !== '', function ($query) {
                $query->where('category', $this->category);
            })
            ->when($this->isPublic !== '', function ($query) {
                $query->where('is_public', filter_var($this->isPublic, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('starts_at', 'desc')
            ->paginate(25);

        $categories = Event::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        return view('livewire.events.index', [
            'events' => $events,
            'categories' => $categories,
        ])->layout('layouts.app', ['header' => 'Events Calendar']);
    }
}
