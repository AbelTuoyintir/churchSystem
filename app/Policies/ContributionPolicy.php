<?php

namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;

class ContributionPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function view(User $user, Contribution $contribution): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return $user->person_id !== null && (int) $user->person_id === (int) $contribution->person_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff'], true);
    }

    public function update(User $user, Contribution $contribution): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function delete(User $user, Contribution $contribution): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }
}
