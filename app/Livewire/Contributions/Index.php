<?php

namespace App\Livewire\Contributions;

use App\Models\Contribution;
use App\Models\Fund;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $date_from = '';
    public string $date_to = '';
    public string $fund_id = '';
    public string $method = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'fund_id' => ['except' => ''],
        'method' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingFundId(): void
    {
        $this->resetPage();
    }

    public function updatingMethod(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', Contribution::class);

        $contributions = Contribution::query()
            ->with(['person', 'fund'])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('person', function ($pq) {
                        $pq->where('first_name', 'like', '%' . $this->search . '%')
                           ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('reference', 'like', '%' . $this->search . '%')
                    ->orWhere('note', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->date_from !== '', function ($query) {
                $query->whereDate('contributed_on', '>=', $this->date_from);
            })
            ->when($this->date_to !== '', function ($query) {
                $query->whereDate('contributed_on', '<=', $this->date_to);
            })
            ->when($this->fund_id !== '', function ($query) {
                $query->where('fund_id', $this->fund_id);
            })
            ->when($this->method !== '', function ($query) {
                $query->where('method', $this->method);
            })
            ->orderBy('contributed_on', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);

        $funds = Fund::orderBy('name')->get();

        return view('livewire.contributions.index', [
            'contributions' => $contributions,
            'funds' => $funds,
        ])->layout('layouts.app');
    }
}
