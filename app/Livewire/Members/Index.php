<?php

namespace App\Livewire\Members;

use App\Models\Person;
use Livewire\Component;

class Index extends Component
{
    public bool $isEditingProfile = false;

    public ?string $email = null;
    public ?string $phone = null;
    public ?string $address_line1 = null;
    public ?string $address_line2 = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $postal_code = null;

    public function editProfile()
    {
        $user = auth()->user();
        if (!$user || !$user->person_id) {
            return;
        }

        $person = Person::find($user->person_id);
        if (!$person) {
            return;
        }

        $this->authorize('update', $person);

        $this->email = $person->email;
        $this->phone = $person->phone;
        $this->address_line1 = $person->address_line1;
        $this->address_line2 = $person->address_line2;
        $this->city = $person->city;
        $this->state = $person->state;
        $this->postal_code = $person->postal_code;

        $this->isEditingProfile = true;
    }

    public function cancelEditProfile()
    {
        $this->isEditingProfile = false;
        $this->resetValidation();
    }

    public function updateProfile()
    {
        $user = auth()->user();
        if (!$user || !$user->person_id) {
            return;
        }

        $person = Person::find($user->person_id);
        if (!$person) {
            return;
        }

        $this->authorize('update', $person);

        $validated = $this->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $person->update($validated);

        $this->isEditingProfile = false;
        session()->flash('message', 'Profile updated successfully.');
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
