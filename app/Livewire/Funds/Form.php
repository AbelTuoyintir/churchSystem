<?php

namespace App\Livewire\Funds;

use App\Models\Fund;
use Livewire\Component;

class Form extends Component
{
    public ?Fund $fund = null;

    public string $name = '';
    public string $description = '';
    public bool $is_tax_deductible = true;
    public ?string $goal_amount = null;
    public bool $is_active = true;

    public function mount(?Fund $fund = null): void
    {
        if ($fund && $fund->exists) {
            $this->authorize('view', $fund);
            $this->fund = $fund;

            $this->name = $fund->name ?? '';
            $this->description = $fund->description ?? '';
            $this->is_tax_deductible = (bool) ($fund->is_tax_deductible ?? true);
            $this->goal_amount = $fund->goal_amount !== null ? (string) $fund->goal_amount : null;
            $this->is_active = (bool) ($fund->is_active ?? true);
        } else {
            $this->authorize('create', Fund::class);
            $this->fund = new Fund();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_tax_deductible' => 'boolean',
            'goal_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->fund && $this->fund->exists) {
            $this->authorize('update', $this->fund);
        } else {
            $this->authorize('create', Fund::class);
        }

        $validated = $this->validate();

        if (empty($validated['goal_amount']) && $validated['goal_amount'] !== '0') {
            $validated['goal_amount'] = null;
        }

        if ($this->fund && $this->fund->exists) {
            $this->fund->update($validated);
            session()->flash('success', 'Fund updated successfully.');
        } else {
            $this->fund = Fund::create($validated);
            session()->flash('success', 'Fund created successfully.');
        }

        return redirect()->route('funds.index');
    }

    public function render()
    {
        return view('livewire.funds.form')->layout('layouts.app');
    }
}
