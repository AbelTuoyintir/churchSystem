<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;

class PersonPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Person $person): bool
    {
        if (in_array($user->role, ['admin', 'staff', 'leader', 'viewer'])) {
            return true;
        }

        return $user->person_id !== null && (int) $user->person_id === (int) $person->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Person $person): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function delete(User $user, Person $person): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function viewNotes(User $user, ?Person $person = null): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
