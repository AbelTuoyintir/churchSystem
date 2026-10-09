<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Assignment $assignment): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }
}
