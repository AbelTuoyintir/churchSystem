<?php

namespace App\Livewire\Households;

use App\Models\Household;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $state = '';
    public bool $confirmingDeletion = false;
    public ?int $householdIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'state' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingState(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->householdIdBeingDeleted = $id;
        $this->confirmingDeletion = true;
    }

    public function deleteHousehold(): void
    {
        $household = Household::findOrFail($this->householdIdBeingDeleted);
        $this->authorize('delete', $household);

        $household->delete();

        $this->confirmingDeletion = false;
        $this->householdIdBeingDeleted = null;

        session()->flash('success', 'Household deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Household::class);

        $households = Household::query()
            ->withCount('members')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->when($this->state, function ($query) {
                $query->where('state', 'like', '%' . $this->state . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(25);

        return view('livewire.households.index', [
            'households' => $households,
        ])->layout('layouts.app');
    }
}
