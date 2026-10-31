<?php

namespace App\Livewire\PrayerRequests;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\PrayerRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $title = '';

    public string $body = '';

    public string $scope = 'public';

    public ?int $group_id = null;

    protected function rules(): array
    {
        $personId = Auth::user()?->person_id;

        $allowedGroupIds = $personId
            ? GroupMember::query()
                ->where('person_id', $personId)
                ->whereNull('left_at')
                ->pluck('group_id')
                ->merge(Group::query()->where('leader_id', $personId)->pluck('id'))
                ->merge(Group::query()->whereHas('members', fn ($query) => $query
                    ->where('person_id', $personId)
                    ->where('role', 'leader')
                    ->whereNull('left_at'))->pluck('id'))
                ->unique()
                ->all()
            : [];

        return [
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|max:2000',
            'scope' => ['required', Rule::in(['public', 'group', 'pastoral'])],
            'group_id' => [
                'nullable',
                'required_if:scope,group',
                Rule::in($allowedGroupIds),
            ],
        ];
    }

    public function submit(): void
    {
        $personId = Auth::user()?->person_id;
        abort_unless($personId, 403);

        $validated = $this->validate();

        if ($validated['scope'] !== 'group') {
            $validated['group_id'] = null;
        }

        PrayerRequest::create([
            'person_id' => $personId,
            'title' => $validated['title'] ?: null,
            'body' => $validated['body'],
            'scope' => $validated['scope'],
            'group_id' => $validated['group_id'] ?? null,
            'status' => 'open',
        ]);

        $this->reset(['title', 'body', 'scope', 'group_id']);
        $this->scope = 'public';

        session()->flash('success', 'Your prayer request has been submitted.');
    }

    public function render()
    {
        $personId = Auth::user()?->person_id;

        $publicWall = PrayerRequest::query()
            ->with(['person:id,first_name,last_name,photo_path', 'group:id,name'])
            ->where('scope', 'public')
            ->latest()
            ->paginate(10, ['*'], 'wallPage');

        $myRequests = $personId
            ? PrayerRequest::query()
                ->with('group:id,name')
                ->where('person_id', $personId)
                ->latest()
                ->get()
            : collect();

        $myGroups = $personId
            ? Group::query()
                ->whereIn('id', GroupMember::query()->where('person_id', $personId)->whereNull('left_at')->pluck('group_id')
                    ->merge(Group::query()->where('leader_id', $personId)->pluck('id'))
                    ->merge(Group::query()->whereHas('members', fn ($query) => $query
                        ->where('person_id', $personId)
                        ->where('role', 'leader')
                        ->whereNull('left_at'))->pluck('id'))
                    ->unique())
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
            : collect();

        return view('livewire.prayer-requests.index', [
            'publicWall' => $publicWall,
            'myRequests' => $myRequests,
            'myGroups' => $myGroups,
        ])->layout('layouts.app', ['header' => 'Prayer Requests']);
    }
}
