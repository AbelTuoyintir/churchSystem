<?php

namespace App\Livewire\Contributions;

use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Person;
use Livewire\Component;

class Form extends Component
{
    public ?Contribution $contribution = null;

    public ?int $person_id = null;
    public ?int $fund_id = null;
    public string $amount = '';
    public string $contributed_on = '';
    public string $method = 'cash';
    public string $reference = '';
    public bool $is_anonymous = false;
    public string $note = '';

    public string $personSearch = '';

    public function mount(?Contribution $contribution = null): void
    {
        if ($contribution && $contribution->exists) {
            $this->authorize('view', $contribution);
            $this->contribution = $contribution;

            $this->person_id = $contribution->person_id;
            $this->fund_id = $contribution->fund_id;
            $this->amount = (string) $contribution->amount;
            $this->contributed_on = $contribution->contributed_on ? $contribution->contributed_on->format('Y-m-d') : now()->format('Y-m-d');
            $this->method = $contribution->method ?? 'cash';
            $this->reference = $contribution->reference ?? '';
            $this->is_anonymous = (bool) ($contribution->is_anonymous ?? false);
            $this->note = $contribution->note ?? '';
        } else {
            $this->authorize('create', Contribution::class);
            $this->contribution = new Contribution();
            $this->contributed_on = now()->format('Y-m-d');

            // Default to first active fund if available
            $defaultFund = Fund::where('is_active', true)->first();
            if ($defaultFund) {
                $this->fund_id = $defaultFund->id;
            }
        }
    }

    public function updatedIsAnonymous($value): void
    {
        if ($value) {
            $this->person_id = null;
            $this->personSearch = '';
        }
    }

    protected function rules(): array
    {
        return [
            'person_id' => 'nullable|exists:people,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0.01',
            'contributed_on' => 'required|date',
            'method' => 'required|string|in:cash,check,card,transfer,online',
            'reference' => 'nullable|string|max:255',
            'is_anonymous' => 'boolean',
            'note' => 'nullable|string',
        ];
    }

    public function save()
    {
        if ($this->contribution && $this->contribution->exists) {
            $this->authorize('update', $this->contribution);
        } else {
            $this->authorize('create', Contribution::class);
        }

        if ($this->is_anonymous) {
            $this->person_id = null;
        }

        $validated = $this->validate();
        $validated['recorded_by'] = auth()->id();

        if ($this->contribution && $this->contribution->exists) {
            $this->contribution->update($validated);
            session()->flash('success', 'Contribution updated successfully.');
        } else {
            $this->contribution = Contribution::create($validated);
            session()->flash('success', 'Contribution recorded successfully.');
        }

        return redirect()->route('contributions.index');
    }

    public function render()
    {
        $funds = Fund::where('is_active', true)->orderBy('name')->get();

        $peopleQuery = Person::query()->orderBy('last_name')->orderBy('first_name');
        if ($this->personSearch !== '') {
            $peopleQuery->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->personSearch . '%')
                  ->orWhere('last_name', 'like', '%' . $this->personSearch . '%');
            });
        }
        $people = $peopleQuery->limit(50)->get();

        return view('livewire.contributions.form', [
            'funds' => $funds,
            'people' => $people,
        ])->layout('layouts.app');
    }
}
