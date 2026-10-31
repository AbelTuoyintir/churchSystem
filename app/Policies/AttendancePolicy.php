<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'viewer'], true);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff', 'viewer'], true)
            || ($attendance->group_id !== null && $attendance->service_id === null && $attendance->event_id === null
                && Group::query()->ledBy($user)->whereKey($attendance->group_id)->exists());
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff'], true);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff'], true)
            || ($attendance->group_id !== null && $attendance->service_id === null && $attendance->event_id === null
                && Group::query()->ledBy($user)->whereKey($attendance->group_id)->exists());
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'staff'], true)
            || ($attendance->group_id !== null && $attendance->service_id === null && $attendance->event_id === null
                && Group::query()->ledBy($user)->whereKey($attendance->group_id)->exists());
    }
}
