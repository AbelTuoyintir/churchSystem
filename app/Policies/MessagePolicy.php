<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Message $message): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function update(User $user, Message $message): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function delete(User $user, Message $message): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function send(User $user, Message $message): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
