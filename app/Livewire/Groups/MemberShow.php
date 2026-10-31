<?php

namespace App\Livewire\Groups;

use App\Models\Event;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;

class MemberShow extends Component
{
    public Group $group;

    public function mount(Group $group): void
    {
        $this->group = $group;
        $this->ensureMemberAccess();
    }

    public function leaveGroup(): RedirectResponse
    {
        $this->ensureMemberAccess();

        $membership = $this->group->members()
            ->where('person_id', auth()->user()->person_id)
            ->whereNull('left_at')
            ->firstOrFail();

        $membership->update(['left_at' => now()->toDateString()]);

        session()->flash('success', 'You have left the group.');

        return redirect()->route('members.groups');
    }

    public function render()
    {
        $this->ensureMemberAccess();

        $group = $this->group->load('leader');
        $members = $group->members()
            ->with('person:id,first_name,last_name,preferred_name,photo_path,directory_opt_in,email,phone')
            ->whereNull('left_at')
            ->orderBy('role')
            ->get();

        $events = Event::query()
            ->whereHas('attendances', fn ($query) => $query->where('group_id', $group->id))
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        $announcements = Message::query()
            ->where('status', 'sent')
            ->where('audience_filter->target_audience', 'group')
            ->where('audience_filter->group_id', $group->id)
            ->latest('sent_at')
            ->get();

        return view('livewire.groups.member-show', [
            'group' => $group,
            'members' => $members,
            'events' => $events,
            'announcements' => $announcements,
        ])->layout('layouts.app', ['header' => $group->name]);
    }

    private function ensureMemberAccess(): void
    {
        $personId = auth()->user()?->person_id;

        abort_unless($personId && (
            $this->group->members()
                ->where('person_id', $personId)
                ->whereNull('left_at')
                ->exists()
            || (int) $this->group->leader_id === (int) $personId
        ), 404);
    }
}
