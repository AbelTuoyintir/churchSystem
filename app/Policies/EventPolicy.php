<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Group;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->isStaff() || $event->is_public) {
            return true;
        }

        return $user->person_id !== null && $event->group_id !== null && Group::query()
            ->whereKey($event->group_id)
            ->where(function ($query) use ($user): void {
                $query->where('leader_id', $user->person_id)
                    ->orWhereHas('members', fn ($members) => $members
                        ->where('person_id', $user->person_id)
                        ->whereNull('left_at'));
            })
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isStaff() || $user->isGroupLeader();
    }

    public function update(User $user, Event $event): bool
    {
        return $user->isStaff() || ($event->group_id !== null
            && Group::query()->ledBy($user)->whereKey($event->group_id)->exists());
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}
