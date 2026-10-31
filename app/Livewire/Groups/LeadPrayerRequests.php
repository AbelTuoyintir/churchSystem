<?php

namespace App\Livewire\Groups;

use App\Models\Group;
use App\Models\PrayerRequest;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class LeadPrayerRequests extends Component
{
    public string $groupId = '';

    public string $title = '';

    public string $body = '';

    public function mount(?int $groupId = null): void
    {
        $user = auth()->user();
        abort_unless($user->isGroupLeader(), 403);
        $this->groupId = (string) ($groupId && Group::query()->ledBy($user)->whereKey($groupId)->exists()
            ? $groupId
            : Group::query()->ledBy($user)->orderBy('name')->value('id'));
    }

    public function save(): void
    {
        $group = Group::query()->ledBy(auth()->user())->findOrFail($this->groupId);
        $validated = $this->validate([
            'groupId' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        PrayerRequest::query()->create([
            'person_id' => auth()->user()->person_id,
            'group_id' => $group->id,
            'title' => $validated['title'] ?: null,
            'body' => $validated['body'],
            'scope' => 'group',
            'status' => 'open',
        ]);

        $this->reset(['title', 'body']);
        session()->flash('success', 'Group prayer request added.');
    }

    public function updateStatus(int $requestId, string $status): void
    {
        abort_unless(in_array($status, ['open', 'praying', 'answered'], true), 422);
        $this->ownedRequests()->findOrFail($requestId)->update(['status' => $status]);
    }

    public function render()
    {
        $user = auth()->user();
        $groups = Group::query()->ledBy($user)->orderBy('name')->get();
        $requests = $this->ownedRequests()->with(['person:id,first_name,last_name', 'group:id,name'])
            ->latest()->paginate(20);

        return view('livewire.groups.lead-prayer-requests', [
            'groups' => $groups,
            'requests' => $requests,
        ])->layout('layouts.app', ['header' => 'Group Prayer Requests']);
    }

    private function ownedRequests(): Builder
    {
        return PrayerRequest::query()
            ->where('scope', 'group')
            ->whereIn('group_id', Group::query()->ledBy(auth()->user())->select('id'))
            ->when($this->groupId !== '', fn ($query) => $query->where('group_id', $this->groupId));
    }
}
