<?php

namespace App\Livewire\People;

use App\Models\Household;
use App\Models\Person;
use App\Services\CustomerPasswordService;
use Livewire\Component;

class Form extends Component
{
    public ?Person $person = null;

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $preferred_name = '';
    public string $email = '';
    public string $phone = '';
    public string $alternate_phone = '';
    public ?string $date_of_birth = null;
    public string $gender = '';
    public string $marital_status = '';
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = '';
    public string $photo_path = '';
    public string $membership_status = 'visitor';
    public ?string $joined_at = null;
    public ?string $baptized_at = null;
    public bool $email_opt_in = true;
    public bool $sms_opt_in = false;
    public string $notes = '';
    public bool $is_active = true;

    // Household attachment properties
    public string $selectedHouseholdId = '';
    public string $householdRole = 'other';
    public bool $confirmingHouseholdDetach = false;
    public ?int $householdIdToDetach = null;

    // Password generation properties
    public bool $confirmingPasswordGeneration = false;
    public string $passwordChannel = 'auto';
    public ?string $generatedPasswordNotice = null;

    public function mount(?Person $person = null): void
    {
        if ($person && $person->exists) {
            $this->authorize('view', $person);
            $this->person = $person;

            $this->first_name = $person->first_name ?? '';
            $this->middle_name = $person->middle_name ?? '';
            $this->last_name = $person->last_name ?? '';
            $this->preferred_name = $person->preferred_name ?? '';
            $this->email = $person->email ?? '';
            $this->phone = $person->phone ?? '';
            $this->alternate_phone = $person->alternate_phone ?? '';
            $this->date_of_birth = $person->date_of_birth ? $person->date_of_birth->format('Y-m-d') : null;
            $this->gender = $person->gender ?? '';
            $this->marital_status = $person->marital_status ?? '';
            $this->address_line1 = $person->address_line1 ?? '';
            $this->address_line2 = $person->address_line2 ?? '';
            $this->city = $person->city ?? '';
            $this->state = $person->state ?? '';
            $this->postal_code = $person->postal_code ?? '';
            $this->country = $person->country ?? '';
            $this->photo_path = $person->photo_path ?? '';
            $this->membership_status = $person->membership_status ?? 'visitor';
            $this->joined_at = $person->joined_at ? $person->joined_at->format('Y-m-d') : null;
            $this->baptized_at = $person->baptized_at ? $person->baptized_at->format('Y-m-d') : null;
            $this->email_opt_in = (bool) $person->email_opt_in;
            $this->sms_opt_in = (bool) $person->sms_opt_in;
            $this->notes = $person->notes ?? '';
            $this->is_active = (bool) $person->is_active;
        } else {
            $this->authorize('create', Person::class);
            $this->person = new Person();
        }
    }

    protected function rules(): array
    {
        $personId = $this->person && $this->person->exists ? $this->person->id : null;

        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'preferred_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:people,email,' . ($personId ?: 'NULL') . ',id,deleted_at,NULL',
            'phone' => 'nullable|string|max:255',
            'alternate_phone' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'photo_path' => 'nullable|string|max:255',
            'membership_status' => 'required|string|in:visitor,member,inactive',
            'joined_at' => 'nullable|date',
            'baptized_at' => 'nullable|date',
            'email_opt_in' => 'boolean',
            'sms_opt_in' => 'boolean',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->person && $this->person->exists) {
            $this->authorize('update', $this->person);
        } else {
            $this->authorize('create', Person::class);
        }

        $validated = $this->validate();

        $validated['date_of_birth'] = $validated['date_of_birth'] ?: null;
        $validated['joined_at'] = $validated['joined_at'] ?: null;
        $validated['baptized_at'] = $validated['baptized_at'] ?: null;

        if ($this->person && $this->person->exists) {
            $this->person->update($validated);
            session()->flash('success', 'Person updated successfully.');
        } else {
            $this->person = Person::create($validated);
            session()->flash('success', 'Person created successfully.');
        }

        return redirect()->route('people.index');
    }

    public function confirmGeneratePassword(): void
    {
        if (!$this->person || !$this->person->exists) {
            return;
        }

        $this->passwordChannel = 'auto';
        $this->confirmingPasswordGeneration = true;
    }

    public function generatePassword(CustomerPasswordService $service): void
    {
        if (!$this->person || !$this->person->exists) {
            return;
        }

        $this->authorize('generatePassword', $this->person);

        $result = $service->generateAndSendForPerson($this->person, $this->passwordChannel, auth()->user());

        $this->confirmingPasswordGeneration = false;
        $this->generatedPasswordNotice = "Password for {$this->person->full_name}: {$result['password']}";
        session()->flash('password_success', $result['message']);
    }

    public function attachHousehold(): void
    {
        if (!$this->person || !$this->person->exists) {
            return;
        }

        $this->authorize('update', $this->person);

        $this->validate([
            'selectedHouseholdId' => 'required|exists:households,id',
            'householdRole' => 'required|string|in:head,spouse,child,other',
        ]);

        $this->person->households()->syncWithoutDetaching([
            $this->selectedHouseholdId => ['role' => $this->householdRole]
        ]);

        $this->reset(['selectedHouseholdId']);
        $this->householdRole = 'other';
        session()->flash('household_success', 'Household attached successfully.');
    }

    public function confirmDetachHousehold(int $householdId): void
    {
        $this->householdIdToDetach = $householdId;
        $this->confirmingHouseholdDetach = true;
    }

    public function detachHousehold(): void
    {
        if (!$this->person || !$this->person->exists) {
            return;
        }

        $this->authorize('update', $this->person);

        if ($this->householdIdToDetach) {
            $this->person->households()->detach($this->householdIdToDetach);
        }

        $this->confirmingHouseholdDetach = false;
        $this->householdIdToDetach = null;

        session()->flash('household_success', 'Household detached successfully.');
    }

    public function render()
    {
        $allHouseholds = Household::orderBy('name')->get();
        $attachedHouseholds = ($this->person && $this->person->exists)
            ? $this->person->households()->withPivot('role')->get()
            : collect();

        return view('livewire.people.form', [
            'allHouseholds' => $allHouseholds,
            'attachedHouseholds' => $attachedHouseholds,
        ])->layout('layouts.app');
    }
}
