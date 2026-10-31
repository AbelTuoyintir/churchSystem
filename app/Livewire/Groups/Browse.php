<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\GroupJoinRequest;
use App\Models\GroupMember;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    public string $search = '';

    public string $type = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function requestToJoin(int $groupId): void
    {
        $personId = auth()->user()?->person_id;
        abort_unless($personId, 403);

        $group = Group::query()
            ->where('is_active', true)
            ->where('is_open', true)
            ->findOrFail($groupId);

        if (GroupMember::query()
            ->where('group_id', $group->id)
            ->where('person_id', $personId)
            ->whereNull('left_at')
            ->exists()) {
            session()->flash('success', 'You are already part of this group.');

            return;
        }

        DB::transaction(function () use ($group, $personId): void {
            GroupJoinRequest::query()->updateOrCreate(
                ['group_id' => $group->id, 'person_id' => $personId],
                ['status' => 'pending'],
            );
        });

        session()->flash('success', 'Your request to join has been sent.');
    }

    public function render()
    {
        $groups = Group::query()
            ->with('leader')
            ->where('is_active', true)
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->type !== '', fn ($query) => $query->where('type', $this->type))
            ->orderBy('name')
            ->paginate(12);

        $personId = auth()->user()?->person_id;
        $groupIds = $groups->getCollection()->modelKeys();
        $memberGroupIds = $personId
            ? GroupMember::query()->where('person_id', $personId)->whereNull('left_at')->whereIn('group_id', $groupIds)->pluck('group_id')
            : collect();
        $pendingGroupIds = $personId
            ? GroupJoinRequest::query()->where('person_id', $personId)->where('status', 'pending')->whereIn('group_id', $groupIds)->pluck('group_id')
            : collect();

        return view('livewire.groups.browse', [
            'groups' => $groups,
            'memberGroupIds' => $memberGroupIds,
            'pendingGroupIds' => $pendingGroupIds,
        ])->layout('layouts.app', ['header' => 'Find a Group']);
    }
}
