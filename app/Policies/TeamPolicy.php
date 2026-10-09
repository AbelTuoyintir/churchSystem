<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Team $team): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function update(User $user, Team $team): bool
    {
        if (in_array($user->role, ['admin', 'staff'])) {
            return true;
        }

        if ($user->role === 'leader' && $user->person_id) {
            return (int) $team->leader_id === (int) $user->person_id;
        }

        return false;
    }

    public function delete(User $user, Team $team): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
