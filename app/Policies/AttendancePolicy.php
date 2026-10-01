<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader', 'viewer']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return in_array($user->role, ['admin', 'staff', 'leader']);
    }
}
