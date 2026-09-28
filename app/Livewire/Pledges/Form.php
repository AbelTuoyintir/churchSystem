<?php

namespace App\Livewire\Pledges;

use App\Models\Fund;
use App\Models\Person;
use App\Models\Pledge;
use Livewire\Component;

class Form extends Component
{
    public ?Pledge $pledge = null;

    public ?int $person_id = null;
    public ?int $fund_id = null;
    public string $amount = '';
    public string $frequency = 'monthly';
    public string $start_date = '';
    public ?string $end_date = null;

    public string $personSearch = '';

    public function mount(?Pledge $pledge = null): void
    {
        if ($pledge && $pledge->exists) {
            $this->authorize('view', $pledge);
            $this->pledge = $pledge;

            $this->person_id = $pledge->person_id;
            $this->fund_id = $pledge->fund_id;
            $this->amount = (string) $pledge->amount;
            $this->frequency = $pledge->frequency ?? 'monthly';
            $this->start_date = $pledge->start_date ? $pledge->start_date->format('Y-m-d') : now()->format('Y-m-d');
            $this->end_date = $pledge->end_date ? $pledge->end_date->format('Y-m-d') : null;
        } else {
            $this->authorize('create', Pledge::class);
            $this->pledge = new Pledge();
            $this->start_date = now()->format('Y-m-d');

            $defaultFund = Fund::where('is_active', true)->first();
            if ($defaultFund) {
                $this->fund_id = $defaultFund->id;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'person_id' => 'required|exists:people,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|string|in:one_time,weekly,monthly,annual',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function save()
    {
        if ($this->pledge && $this->pledge->exists) {
            $this->authorize('update', $this->pledge);
        } else {
            $this->authorize('create', Pledge::class);
        }

        $validated = $this->validate();

        if (empty($validated['end_date'])) {
            $validated['end_date'] = null;
        }

        if ($this->pledge && $this->pledge->exists) {
            $this->pledge->update($validated);
            session()->flash('success', 'Pledge updated successfully.');
        } else {
            $this->pledge = Pledge::create($validated);
            session()->flash('success', 'Pledge created successfully.');
        }

        return redirect()->route('pledges.index');
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

        return view('livewire.pledges.form', [
            'funds' => $funds,
            'people' => $people,
        ])->layout('layouts.app');
    }
}
