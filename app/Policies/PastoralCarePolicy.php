<?php

namespace App\Policies;

use App\Models\PastoralCareNote;
use App\Models\User;

class PastoralCarePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor'], true);
    }

    public function view(User $user, PastoralCareNote $note): bool
    {
        return in_array($user->role, ['admin', 'pastor'], true);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor'], true);
    }

    public function update(User $user, PastoralCareNote $note): bool
    {
        return in_array($user->role, ['admin', 'pastor'], true);
    }

    public function delete(User $user, PastoralCareNote $note): bool
    {
        return in_array($user->role, ['admin', 'pastor'], true);
    }
}
