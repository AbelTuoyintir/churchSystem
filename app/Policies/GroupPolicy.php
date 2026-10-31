<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'viewer', 'leader'], true) || $user->isGroupLeader();
    }

    public function view(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'viewer'], true)
            || ($user->isGroupLeader() && Group::query()->ledBy($user)->whereKey($group->id)->exists());
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }

    public function update(User $user, Group $group): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'], true)) {
            return true;
        }

        return $user->isGroupLeader() && Group::query()->ledBy($user)->whereKey($group->id)->exists();
    }

    public function delete(User $user, Group $group): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff']);
    }
}
