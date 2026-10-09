<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Event $event): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function update(User $user, Event $event): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function delete(User $user, Event $event): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
