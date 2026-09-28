<?php

namespace App\Livewire\Funds;

use App\Models\Fund;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $isActive = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'isActive' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Fund::class);

        $funds = Fund::query()
            ->withSum('contributions', 'amount')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->isActive !== '', function ($query) {
                $query->where('is_active', filter_var($this->isActive, FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('name', 'asc')
            ->paginate(25);

        return view('livewire.funds.index', [
            'funds' => $funds,
        ])->layout('layouts.app');
    }
}
