<?php

namespace App\Policies;

use App\Models\Pledge;
use App\Models\User;

class PledgePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function view(User $user, Pledge $pledge): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Pledge $pledge): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function delete(User $user, Pledge $pledge): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
