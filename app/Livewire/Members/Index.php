<?php

namespace App\Livewire\Members;

use App\Models\Person;
use Livewire\Component;

class Index extends Component
{
    public bool $editingProfile = false;

    public string $preferred_name = '';
    public string $phone = '';
    public string $alternate_phone = '';
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public bool $email_opt_in = true;
    public bool $sms_opt_in = false;

    protected function rules(): array
    {
        return [
            'preferred_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'alternate_phone' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'email_opt_in' => 'boolean',
            'sms_opt_in' => 'boolean',
        ];
    }

    public function openEditProfileModal(): void
    {
        $user = auth()->user();
        if (!$user || !$user->person_id) {
            return;
        }

        $person = Person::findOrFail($user->person_id);
        $this->authorize('update', $person);

        $this->preferred_name = $person->preferred_name ?? '';
        $this->phone = $person->phone ?? '';
        $this->alternate_phone = $person->alternate_phone ?? '';
        $this->address_line1 = $person->address_line1 ?? '';
        $this->address_line2 = $person->address_line2 ?? '';
        $this->city = $person->city ?? '';
        $this->state = $person->state ?? '';
        $this->postal_code = $person->postal_code ?? '';
        $this->email_opt_in = (bool) $person->email_opt_in;
        $this->sms_opt_in = (bool) $person->sms_opt_in;

        $this->editingProfile = true;
    }

    public function closeEditProfileModal(): void
    {
        $this->editingProfile = false;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();
        if (!$user || !$user->person_id) {
            return;
        }

        $person = Person::findOrFail($user->person_id);
        $this->authorize('update', $person);

        $validated = $this->validate();

        $person->update($validated);

        $this->editingProfile = false;
        session()->flash('success', 'Profile contact details updated successfully.');
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
