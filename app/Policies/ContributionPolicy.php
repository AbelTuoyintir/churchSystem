<?php

namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;

class ContributionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function view(User $user, Contribution $contribution): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Contribution $contribution): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function delete(User $user, Contribution $contribution): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
