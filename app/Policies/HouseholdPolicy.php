<?php

namespace App\Policies;

use App\Models\Household;
use App\Models\User;

class HouseholdPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Household $household): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'leader', 'viewer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function update(User $user, Household $household): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function delete(User $user, Household $household): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }
}
