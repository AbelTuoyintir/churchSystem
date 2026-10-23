<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'leader', 'viewer']) || $user->isLeader();
    }

    public function view(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'leader', 'viewer']) || $user->isLeader();
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function update(User $user, Group $group): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        if ($user->isLeader()) {
            return $user->person_id !== null && $group->leader_id !== null && (int) $user->person_id === (int) $group->leader_id;
        }

        return false;
    }

    public function delete(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }
}
