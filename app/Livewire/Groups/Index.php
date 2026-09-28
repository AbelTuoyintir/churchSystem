<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $isActive = '';
    public bool $confirmingInactivation = false;
    public ?int $groupIdBeingInactivated = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function confirmInactivate(int $id): void
    {
        $this->groupIdBeingInactivated = $id;
        $this->confirmingInactivation = true;
    }

    public function markInactive(): void
    {
        $group = Group::findOrFail($this->groupIdBeingInactivated);
        $this->authorize('update', $group);

        $group->update(['is_active' => false]);

        $this->confirmingInactivation = false;
        $this->groupIdBeingInactivated = null;

        session()->flash('success', 'Group marked as inactive.');
    }

    public function render()
    {
        $this->authorize('viewAny', Group::class);

        $groups = Group::query()
            ->with('leader')
            ->withCount('members')
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->type !== '', function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->isActive !== '', function ($query) {
                $query->where('is_active', filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('name', 'asc')
            ->paginate(25);

        return view('livewire.groups.index', [
            'groups' => $groups,
        ])->layout('layouts.app');
    }
}
