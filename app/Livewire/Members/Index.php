<?php

namespace App\Livewire\Members;

use App\Models\Person;
use Livewire\Component;

class Index extends Component
{
    public bool $isEditing = false;

    public ?string $phone = null;
    public ?string $address_line1 = null;
    public ?string $address_line2 = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $postal_code = null;
    public ?string $date_of_birth = null;

    protected function rules(): array
    {
        return [
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
        ];
    }

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && $user->person_id) {
            $person = Person::find($user->person_id);
            if ($person) {
                $this->phone = $person->phone;
                $this->address_line1 = $person->address_line1;
                $this->address_line2 = $person->address_line2;
                $this->city = $person->city;
                $this->state = $person->state;
                $this->postal_code = $person->postal_code;
                $this->date_of_birth = $person->date_of_birth ? $person->date_of_birth->format('Y-m-d') : null;
            }
        }
    }

    public function toggleEdit(): void
    {
        $this->isEditing = !$this->isEditing;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        if (!$user || !$user->person_id) {
            session()->flash('error', 'No linked profile found.');
            return;
        }

        $person = Person::find($user->person_id);

        if (!$person) {
            session()->flash('error', 'Person profile not found.');
            return;
        }

        $validated = $this->validate();

        $person->update($validated);

        $this->isEditing = false;

        session()->flash('success', 'Your contact information has been updated successfully!');
    }

    public function render()
    {
        $user = auth()->user();
        $person = null;

        if ($user && $user->person_id) {
            $person = Person::query()
                ->with([
                    'households.people',
                    'groups',
                    'assignments.team',
                    'assignments.position',
                    'assignments.service',
                    'attendances.service',
                ])
                ->find($user->person_id);
        }

        return view('livewire.members.index', [
            'user' => $user,
            'person' => $person,
        ])->layout('layouts.app', ['header' => 'My Profile & Portal']);
    }
}
