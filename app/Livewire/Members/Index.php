<?php

namespace App\Livewire\Members;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public bool $isEditing = false;

    public bool $editingProfile = false;

    public ?string $preferred_name = null;

    public string $email = '';

    public ?string $phone = null;

    public ?string $alternate_phone = null;

    public ?string $address_line1 = null;

    public ?string $address_line2 = null;

    public ?string $city = null;

    public ?string $state = null;

    public ?string $postal_code = null;

    public ?string $country = null;

    public ?string $date_of_birth = null;

    public bool $email_opt_in = true;

    public bool $sms_opt_in = true;

    public bool $directory_opt_in = false;

    public $photo;

    protected function rules(): array
    {
        $personId = auth()->user()?->person_id;

        return [
            'preferred_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('people', 'email')->ignore($personId),
                Rule::unique('users', 'email')->ignore(auth()->id()),
            ],
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'email_opt_in' => 'boolean',
            'sms_opt_in' => 'boolean',
            'directory_opt_in' => 'boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function mount(): void
    {
        $this->loadPersonData();
    }

    protected function loadPersonData(): void
    {
        $user = auth()->user();
        if ($user && $user->person_id) {
            $person = Person::find($user->person_id);
            if ($person) {
                $this->preferred_name = $person->preferred_name;
                $this->email = $person->email ?: $user->email;
                $this->phone = $person->phone;
                $this->alternate_phone = $person->alternate_phone;
                $this->address_line1 = $person->address_line1;
                $this->address_line2 = $person->address_line2;
                $this->city = $person->city;
                $this->state = $person->state;
                $this->postal_code = $person->postal_code;
                $this->country = $person->country;
                $this->date_of_birth = $person->date_of_birth ? $person->date_of_birth->format('Y-m-d') : null;
                $this->email_opt_in = (bool) $person->email_opt_in;
                $this->sms_opt_in = (bool) $person->sms_opt_in;
                $this->directory_opt_in = (bool) $person->directory_opt_in;
            }
        }
    }

    public function toggleEdit(): void
    {
        $this->isEditing = ! $this->isEditing;
        if ($this->isEditing) {
            $this->loadPersonData();
        }
    }

    public function openEditProfileModal(): void
    {
        $this->loadPersonData();
        $this->editingProfile = true;
        $this->isEditing = true;
    }

    public function closeEditProfileModal(): void
    {
        $this->editingProfile = false;
        $this->isEditing = false;
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->person_id) {
            session()->flash('error', 'No linked profile found.');

            return;
        }

        $person = $user->person;

        if (! $person) {
            session()->flash('error', 'Person profile not found.');

            return;
        }

        $validated = $this->validate();
        $photo = $validated['photo'] ?? null;
        unset($validated['photo']);

        $previousPhotoPath = $person->photo_path;
        if ($photo) {
            $validated['photo_path'] = $photo->store('people/photos', 'public');
        }

        $person->update($validated);

        if ($user->email !== $person->email) {
            $user->email = $person->email;
            $user->email_verified_at = null;
            $user->save();
        }

        if ($photo && $previousPhotoPath) {
            Storage::disk('public')->delete($previousPhotoPath);
        }

        $this->isEditing = false;
        $this->editingProfile = false;

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
                    'groups' => fn ($query) => $query->wherePivotNull('left_at'),
                    'assignments.team',
                    'assignments.position',
                    'assignments.service',
                    'attendances.service',
                    'contributions.fund',
                    'pledges.fund',
                ])
                ->find($user->person_id);
        }

        return view('livewire.members.index', [
            'user' => $user,
            'person' => $person,
        ])->layout('layouts.app', ['header' => 'My Profile & Portal']);
    }
}
