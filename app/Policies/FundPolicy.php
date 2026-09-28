<?php

namespace App\Policies;

use App\Models\Fund;
use App\Models\User;

class FundPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function view(User $user, Fund $fund): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Fund $fund): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function delete(User $user, Fund $fund): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
