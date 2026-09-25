<?php

namespace App\Livewire\Households;

use App\Models\Household;
use App\Models\Person;
use Livewire\Component;

class Form extends Component
{
    public ?Household $household = null;

    public string $name = '';
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = '';
    public string $phone = '';

    // Household member attachment properties
    public string $selectedPersonId = '';
    public string $memberRole = 'other';
    public bool $confirmingMemberDetach = false;
    public ?int $personIdToDetach = null;

    public function mount(?Household $household = null): void
    {
        if ($household && $household->exists) {
            $this->authorize('view', $household);
            $this->household = $household;

            $this->name = $household->name ?? '';
            $this->address_line1 = $household->address_line1 ?? '';
            $this->address_line2 = $household->address_line2 ?? '';
            $this->city = $household->city ?? '';
            $this->state = $household->state ?? '';
            $this->postal_code = $household->postal_code ?? '';
            $this->country = $household->country ?? '';
            $this->phone = $household->phone ?? '';
        } else {
            $this->authorize('create', Household::class);
            $this->household = new Household();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ];
    }

    public function save()
    {
        if ($this->household && $this->household->exists) {
            $this->authorize('update', $this->household);
        } else {
            $this->authorize('create', Household::class);
        }

        $validated = $this->validate();

        if ($this->household && $this->household->exists) {
            $this->household->update($validated);
            session()->flash('success', 'Household updated successfully.');
        } else {
            $this->household = Household::create($validated);
            session()->flash('success', 'Household created successfully.');
        }

        return redirect()->route('households.index');
    }

    public function attachMember(): void
    {
        if (!$this->household || !$this->household->exists) {
            return;
        }

        $this->authorize('update', $this->household);

        $this->validate([
            'selectedPersonId' => 'required|exists:people,id',
            'memberRole' => 'required|string|in:head,spouse,child,other',
        ]);

        $this->household->people()->syncWithoutDetaching([
            $this->selectedPersonId => ['role' => $this->memberRole]
        ]);

        $this->reset(['selectedPersonId']);
        $this->memberRole = 'other';
        session()->flash('member_success', 'Household member attached successfully.');
    }

    public function confirmDetachMember(int $personId): void
    {
        $this->personIdToDetach = $personId;
        $this->confirmingMemberDetach = true;
    }

    public function detachMember(): void
    {
        if (!$this->household || !$this->household->exists) {
            return;
        }

        $this->authorize('update', $this->household);

        if ($this->personIdToDetach) {
            $this->household->people()->detach($this->personIdToDetach);
        }

        $this->confirmingMemberDetach = false;
        $this->personIdToDetach = null;

        session()->flash('member_success', 'Household member detached successfully.');
    }

    public function render()
    {
        $allPeople = Person::orderBy('last_name')->orderBy('first_name')->get();
        $attachedMembers = ($this->household && $this->household->exists)
            ? $this->household->people()->withPivot('role')->get()
            : collect();

        return view('livewire.households.form', [
            'allPeople' => $allPeople,
            'attachedMembers' => $attachedMembers,
        ])->layout('layouts.app');
    }
}
