<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'viewer', 'leader'], true) || $user->isGroupLeader();
    }

    public function view(User $user, Message $message): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return (int) $user->id === (int) $message->created_by;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff'], true) || $user->isGroupLeader();
    }

    public function update(User $user, Message $message): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return (int) $user->id === (int) $message->created_by;
    }

    public function delete(User $user, Message $message): bool
    {
        if (in_array($user->role, ['admin', 'pastor'])) {
            return true;
        }

        return (int) $user->id === (int) $message->created_by;
    }

    public function send(User $user, Message $message): bool
    {
        if (in_array($user->role, ['admin', 'pastor', 'staff'], true)) {
            return true;
        }

        $groupId = data_get($message->audience_filter, 'group_id');

        return (int) $user->id === (int) $message->created_by
            && $user->isGroupLeader()
            && data_get($message->audience_filter, 'target_audience') === 'group'
            && $groupId !== null
            && Group::query()->ledBy($user)->whereKey($groupId)->exists();
    }
}
