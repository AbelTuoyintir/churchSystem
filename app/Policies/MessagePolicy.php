<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'leader', 'viewer']) || $user->isLeader();
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
        return in_array($user->role, ['admin', 'pastor', 'staff', 'leader']) || $user->isLeader();
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
        if (in_array($user->role, ['admin', 'pastor', 'staff'])) {
            return true;
        }

        return (int) $user->id === (int) $message->created_by && $user->isLeader();
    }
}
