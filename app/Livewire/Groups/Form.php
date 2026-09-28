<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Person;
use Livewire\Component;

class Form extends Component
{
    public ?Group $group = null;

    public string $name = '';
    public string $description = '';
    public string $type = 'small_group';
    public ?int $leader_id = null;
    public string $meeting_day = '';
    public string $meeting_time = '';
    public string $location = '';
    public bool $is_active = true;

    // Leader search property
    public string $leaderSearch = '';

    // Member attachment properties
    public ?int $selectedPersonId = null;
    public string $memberRole = 'member';
    public string $joinedAt = '';
    public bool $confirmingMemberDetach = false;
    public ?int $groupMemberIdToDetach = null;

    public function mount(?Group $group = null): void
    {
        $this->joinedAt = now()->format('Y-m-d');

        if ($group && $group->exists) {
            $this->authorize('view', $group);
            $this->group = $group;

            $this->name = $group->name ?? '';
            $this->description = $group->description ?? '';
            $this->type = $group->type ?? 'small_group';
            $this->leader_id = $group->leader_id;
            $this->meeting_day = $group->meeting_day ?? '';
            $this->meeting_time = $group->meeting_time ? \Illuminate\Support\Carbon::parse($group->meeting_time)->format('H:i') : '';
            $this->location = $group->location ?? '';
            $this->is_active = (bool) ($group->is_active ?? true);
        } else {
            $this->authorize('create', Group::class);
            $this->group = new Group();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:small_group,ministry,committee,class',
            'leader_id' => 'nullable|exists:people,id',
            'meeting_day' => 'nullable|string|max:255',
            'meeting_time' => 'nullable',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        if ($this->group && $this->group->exists) {
            $this->authorize('update', $this->group);
        } else {
            $this->authorize('create', Group::class);
        }

        $validated = $this->validate();
        if (empty($validated['leader_id'])) {
            $validated['leader_id'] = null;
        }

        if ($this->group && $this->group->exists) {
            $this->group->update($validated);
            session()->flash('success', 'Group updated successfully.');
        } else {
            $this->group = Group::create($validated);
            session()->flash('success', 'Group created successfully.');
        }

        return redirect()->route('groups.index');
    }

    public function attachMember(): void
    {
        if (!$this->group || !$this->group->exists) {
            return;
        }

        $this->authorize('update', $this->group);

        $this->validate([
            'selectedPersonId' => 'required|exists:people,id',
            'memberRole' => 'required|string|in:leader,co_leader,member',
            'joinedAt' => 'required|date',
        ]);

        GroupMember::create([
            'group_id' => $this->group->id,
            'person_id' => $this->selectedPersonId,
            'role' => $this->memberRole,
            'joined_at' => $this->joinedAt,
            'left_at' => null,
        ]);

        $this->reset(['selectedPersonId']);
        $this->memberRole = 'member';
        $this->joinedAt = now()->format('Y-m-d');
        session()->flash('member_success', 'Member attached successfully.');
    }

    public function confirmDetachMember(int $groupMemberId): void
    {
        $this->groupMemberIdToDetach = $groupMemberId;
        $this->confirmingMemberDetach = true;
    }

    public function detachMember(): void
    {
        if (!$this->group || !$this->group->exists) {
            return;
        }

        $this->authorize('update', $this->group);

        if ($this->groupMemberIdToDetach) {
            $memberRecord = GroupMember::where('group_id', $this->group->id)
                ->where('id', $this->groupMemberIdToDetach)
                ->first();

            if ($memberRecord) {
                $memberRecord->update([
                    'left_at' => now()->toDateString(),
                ]);
            }
        }

        $this->confirmingMemberDetach = false;
        $this->groupMemberIdToDetach = null;

        session()->flash('member_success', 'Member marked as left.');
    }

    public function render()
    {
        $peopleQuery = Person::query()->orderBy('last_name')->orderBy('first_name');

        if ($this->leaderSearch !== '') {
            $peopleQuery->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->leaderSearch . '%')
                    ->orWhere('last_name', 'like', '%' . $this->leaderSearch . '%');
            });
        }

        $allPeople = $peopleQuery->get();

        $currentMembers = ($this->group && $this->group->exists)
            ? GroupMember::with('person')
                ->where('group_id', $this->group->id)
                ->whereNull('left_at')
                ->get()
            : collect();

        return view('livewire.groups.form', [
            'allPeople' => $allPeople,
            'currentMembers' => $currentMembers,
        ])->layout('layouts.app');
    }
}
