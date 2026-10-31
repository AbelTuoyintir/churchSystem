<?php

namespace App\Livewire\Households;

use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class MyHousehold extends Component
{
    public ?int $selectedHouseholdId = null;

    public ?string $address_line1 = null;

    public ?string $address_line2 = null;

    public ?string $city = null;

    public ?string $state = null;

    public ?string $postal_code = null;

    public ?string $country = null;

    public ?string $phone = null;

    public function mount(): void
    {
        $household = $this->memberHouseholds()->orderBy('name')->first();

        if ($household) {
            $this->selectHousehold($household->id);
        }
    }

    public function selectHousehold(int $householdId): void
    {
        $household = $this->memberHouseholds()->findOrFail($householdId);

        $this->selectedHouseholdId = $household->id;
        $this->address_line1 = $household->address_line1;
        $this->address_line2 = $household->address_line2;
        $this->city = $household->city;
        $this->state = $household->state;
        $this->postal_code = $household->postal_code;
        $this->country = $household->country;
        $this->phone = $household->phone;
    }

    public function updateHousehold(): void
    {
        $household = $this->memberHouseholds()->findOrFail($this->selectedHouseholdId);

        $validated = $this->validate([
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $household->update($validated);

        session()->flash('success', 'Household contact details updated.');
    }

    public function render()
    {
        $households = $this->memberHouseholds()
            ->with(['people:id,first_name,last_name,preferred_name'])
            ->orderBy('name')
            ->get();

        return view('livewire.households.my-household', [
            'households' => $households,
            'selectedHousehold' => $households->firstWhere('id', $this->selectedHouseholdId),
        ])->layout('layouts.app', ['header' => 'My Household']);
    }

    private function memberHouseholds(): Builder
    {
        return Household::query()->whereHas('people', function (Builder $query): void {
            $query->where('people.id', auth()->user()?->person_id);
        });
    }
}
