<?php

namespace App\Policies;

use App\Models\Pledge;
use App\Models\User;

class PledgePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function view(User $user, Pledge $pledge): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return $user->person_id !== null && (int) $user->person_id === (int) $pledge->person_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'member']);
    }

    public function update(User $user, Pledge $pledge): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return $user->person_id !== null && (int) $user->person_id === (int) $pledge->person_id;
    }

    public function delete(User $user, Pledge $pledge): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }
}
