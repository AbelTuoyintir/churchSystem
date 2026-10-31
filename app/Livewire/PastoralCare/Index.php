<?php

namespace App\Livewire\PastoralCare;

use App\Models\PastoralCareNote;
use App\Models\Person;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = 'all';
    public string $statusFilter = 'all';

    public ?int $editingId = null;
    public ?int $person_id = null;
    public string $type = 'note';
    public string $title = '';
    public string $body = '';
    public string $status = 'open';
    public string $priority = 'normal';
    public ?string $due_date = null;
    public ?string $visited_at = null;
    public string $location = '';
    public bool $is_confidential = true;

    public function mount(): void
    {
        abort_unless(Auth::user()?->role === 'pastor', 403);
    }

    protected function rules(): array
    {
        return [
            'person_id' => ['nullable', 'exists:people,id'],
            'type' => ['required', 'in:note,visit,followup,counselling'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:open,in_progress,completed,archived'],
            'priority' => ['required', 'in:low,normal,high'],
            'due_date' => ['nullable', 'date'],
            'visited_at' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_confidential' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'person_id' => $this->person_id,
            'author_id' => Auth::id(),
            'type' => $this->type,
            'title' => $this->title ?: null,
            'body' => $this->body,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date ?: null,
            'visited_at' => $this->visited_at ? $this->visited_at.' 00:00:00' : null,
            'location' => $this->location ?: null,
            'is_confidential' => $this->is_confidential,
        ];

        if ($this->editingId) {
            $note = PastoralCareNote::findOrFail($this->editingId);
            $note->update($payload);
            session()->flash('success', 'Pastoral care entry updated.');
        } else {
            PastoralCareNote::create($payload);
            session()->flash('success', 'Pastoral care entry created.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $note = PastoralCareNote::findOrFail($id);
        $this->editingId = $note->id;
        $this->person_id = $note->person_id;
        $this->type = $note->type;
        $this->title = (string) $note->title;
        $this->body = $note->body;
        $this->status = $note->status;
        $this->priority = $note->priority;
        $this->due_date = $note->due_date?->format('Y-m-d');
        $this->visited_at = $note->visited_at?->format('Y-m-d');
        $this->location = (string) $note->location;
        $this->is_confidential = (bool) $note->is_confidential;
    }

    public function delete(int $id): void
    {
        PastoralCareNote::findOrFail($id)->delete();
        session()->flash('success', 'Entry archived.');
    }

    public function markComplete(int $id): void
    {
        PastoralCareNote::findOrFail($id)->update(['status' => 'completed']);
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'person_id', 'title', 'body', 'due_date', 'visited_at', 'location']);
        $this->type = 'note';
        $this->status = 'open';
        $this->priority = 'normal';
        $this->is_confidential = true;
    }

    public function render()
    {
        $query = PastoralCareNote::query()->with(['person:id,first_name,last_name', 'author:id,name']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('body', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $notes = $query->latest()->paginate(12);

        $people = Person::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('livewire.pastoral-care.index', [
            'notes' => $notes,
            'people' => $people,
        ])->layout('layouts.app', ['header' => 'Pastoral Care']);
    }
}

