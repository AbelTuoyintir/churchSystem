<?php

namespace App\Livewire\Giving;

use App\Models\Contribution;
use App\Models\Pledge;
use Livewire\Component;
use Livewire\WithPagination;

class MyGiving extends Component
{
    use WithPagination;

    public string $selectedYear = '';

    public function mount(): void
    {
        $this->selectedYear = (string) now()->year;
    }

    public function render()
    {
        $user = auth()->user();
        $personId = $user?->person_id;

        $contributionsQuery = Contribution::with(['fund', 'pledge'])
            ->where(function ($q) use ($personId, $user) {
                if ($personId) {
                    $q->where('person_id', $personId);
                } else {
                    $q->where('recorded_by', $user?->id);
                }
            });

        if ($this->selectedYear !== '') {
            $contributionsQuery->whereYear('contributed_on', $this->selectedYear);
        }

        $totalGivenThisYear = (clone $contributionsQuery)->sum('amount');
        $contributions = $contributionsQuery->latest('contributed_on')->paginate(10);

        $pledges = collect();
        if ($personId) {
            $pledges = Pledge::with(['fund', 'contributions'])
                ->where('person_id', $personId)
                ->latest()
                ->get();
        }

        return view('livewire.giving.my-giving', [
            'contributions' => $contributions,
            'pledges' => $pledges,
            'totalGivenThisYear' => $totalGivenThisYear,
            'currency' => config('paystack.currency', 'GHS'),
        ])->layout('layouts.app', ['header' => 'My Giving & Pledges']);
    }
}
