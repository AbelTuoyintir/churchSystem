<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;

class PersonPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'viewer'], true) || $user->isGroupLeader();
    }

    public function view(User $user, Person $person): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff', 'viewer'], true)) {
            return true;
        }

        if ($user->person_id !== null && (int) $user->person_id === (int) $person->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function update(User $user, Person $person): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return $user->person_id !== null && (int) $user->person_id === (int) $person->id;
    }

    public function delete(User $user, Person $person): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function viewNotes(User $user, ?Person $person = null): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function generatePassword(User $user, Person $person): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
