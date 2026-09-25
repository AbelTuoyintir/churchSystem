<?php

namespace App\Livewire\People;

use App\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $membershipStatus = '';
    public string $isActive = '';
    public bool $confirmingDeletion = false;
    public ?int $personIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'membershipStatus' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMembershipStatus(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->personIdBeingDeleted = $id;
        $this->confirmingDeletion = true;
    }

    public function deletePerson(): void
    {
        $person = Person::findOrFail($this->personIdBeingDeleted);
        $this->authorize('delete', $person);

        $person->delete();

        $this->confirmingDeletion = false;
        $this->personIdBeingDeleted = null;

        session()->flash('success', 'Person deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Person::class);

        $people = Person::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->membershipStatus !== '', function ($query) {
                $query->where('membership_status', $this->membershipStatus);
            })
            ->when($this->isActive !== '', function ($query) {
                $query->where('is_active', (bool) $this->isActive);
            })
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->paginate(25);

        return view('livewire.people.index', [
            'people' => $people,
        ])->layout('layouts.app');
    }
}
