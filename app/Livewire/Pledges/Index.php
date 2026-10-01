<?php

namespace App\Livewire\Pledges;

use App\Models\Fund;
use App\Models\Pledge;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $fund_id = '';
    public string $frequency = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'fund_id' => ['except' => ''],
        'frequency' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFundId(): void
    {
        $this->resetPage();
    }

    public function updatingFrequency(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Pledge::class);

        $pledges = Pledge::query()
            ->with(['person', 'fund'])
            ->when($this->search !== '', function ($query) {
                $query->whereHas('person', function ($pq) {
                    $pq->where('first_name', 'like', '%' . $this->search . '%')
                       ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->fund_id !== '', function ($query) {
                $query->where('fund_id', $this->fund_id);
            })
            ->when($this->frequency !== '', function ($query) {
                $query->where('frequency', $this->frequency);
            })
            ->orderBy('id', 'desc')
            ->paginate(25);

        $funds = Fund::orderBy('name')->get();

        return view('livewire.pledges.index', [
            'pledges' => $pledges,
            'funds' => $funds,
        ])->layout('layouts.app');
    }
}
