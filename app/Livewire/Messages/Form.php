<?php

namespace App\Livewire\Messages;

use App\Jobs\SendMessageJob;
use App\Models\Group;
use App\Models\Message;
use App\Models\Person;
use App\Models\Team;
use Livewire\Component;

class Form extends Component
{
    public ?Message $message = null;

    public string $subject = '';

    public string $body = '';

    public string $channel = 'email';

    public ?string $scheduled_at = null;

    // Audience selection: 'church' or 'group'
    public string $target_audience = 'church';
    public string $membership_status = '';

    public string $group_id = '';

    public string $team_id = '';

    public bool $email_opt_in = false;

    public bool $sms_opt_in = false;

    public bool $canBroadcastToChurch = true;

    public function mount(?Message $message = null): void
    {
        $user = auth()->user();
        $this->canBroadcastToChurch = $user ? ($user->isAdmin() || $user->isPastor() || $user->role === 'staff') : false;

        if (!$this->canBroadcastToChurch) {
            $this->target_audience = 'group';
        }

        if ($message && $message->exists) {
            $this->authorize('update', $message);
            $this->message = $message;

            $this->subject = $message->subject ?? '';
            $this->body = $message->body ?? '';
            $this->channel = $message->channel ?? 'email';
            $this->scheduled_at = $message->scheduled_at ? $message->scheduled_at->format('Y-m-d\TH:i') : null;

            $filter = $message->audience_filter ?? [];
            $this->target_audience = $filter['target_audience'] ?? (!empty($filter['group_id']) ? 'group' : 'church');
            $this->membership_status = $filter['membership_status'] ?? '';
            $this->group_id = isset($filter['group_id']) ? (string) $filter['group_id'] : '';
            $this->team_id = isset($filter['team_id']) ? (string) $filter['team_id'] : '';
            $this->email_opt_in = ! empty($filter['email_opt_in']);
            $this->sms_opt_in = ! empty($filter['sms_opt_in']);
        } else {
            $this->authorize('create', Message::class);
            $this->message = new Message;
        }
    }

    public function rules(): array
    {
        return [
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'channel' => ['required', 'in:email,sms,both'],
            'scheduled_at' => ['nullable', 'date'],
            'target_audience' => ['required', 'in:church,group'],
            'membership_status' => ['nullable', 'string'],
            'group_id' => [$this->target_audience === 'group' ? 'required' : 'nullable', 'string'],
            'team_id' => ['nullable', 'string'],
            'email_opt_in' => ['boolean'],
            'sms_opt_in' => ['boolean'],
        ];
    }

    public function getMatchingPeopleCountProperty(): int
    {
        $query = Person::query();

        if ($this->channel === 'email') {
            $query->where('email_opt_in', true)
                ->whereNotNull('email')
                ->where('email', '!=', '');
        } elseif ($this->channel === 'sms') {
            $query->where('sms_opt_in', true)
                ->whereNotNull('phone')
                ->where('phone', '!=', '');
        } elseif ($this->channel === 'both') {
            $query->where(function ($q) {
                $q->where(function ($eq) {
                    $eq->where('email_opt_in', true)->whereNotNull('email')->where('email', '!=', '');
                })->orWhere(function ($sq) {
                    $sq->where('sms_opt_in', true)->whereNotNull('phone')->where('phone', '!=', '');
                });
            });
        }

        if ($this->target_audience === 'group' && $this->group_id !== '') {
            $query->whereHas('groups', function ($q) {
                $q->where('groups.id', $this->group_id);
            });
        }

        if ($this->membership_status !== '') {
            $query->where('membership_status', $this->membership_status);
        }

        if ($this->team_id !== '') {
            $query->whereHas('teamMemberships', function ($q) {
                $q->where('team_id', $this->team_id);
            });
        }

        if ($this->email_opt_in) {
            $query->where('email_opt_in', true);
        }

        if ($this->sms_opt_in) {
            $query->where('sms_opt_in', true);
        }

        return $query->count();
    }

    protected function buildData(): array
    {
        $audienceFilter = [
            'target_audience' => $this->target_audience,
            'membership_status' => $this->membership_status !== '' ? $this->membership_status : null,
            'group_id' => ($this->target_audience === 'group' && $this->group_id !== '') ? (int) $this->group_id : null,
            'team_id' => $this->team_id !== '' ? (int) $this->team_id : null,
            'email_opt_in' => $this->email_opt_in,
            'sms_opt_in' => $this->sms_opt_in,
        ];

        $status = 'draft';
        if (! empty($this->scheduled_at) && strtotime($this->scheduled_at) > time()) {
            $status = 'scheduled';
        }

        return [
            'subject' => $this->subject,
            'body' => $this->body,
            'channel' => $this->channel,
            'scheduled_at' => $this->scheduled_at ? $this->scheduled_at : null,
            'status' => $status,
            'audience_filter' => $audienceFilter,
        ];
    }

    public function save(): void
    {
        if ($this->message && $this->message->exists) {
            $this->authorize('update', $this->message);
        } else {
            $this->authorize('create', Message::class);
        }

        $this->validate();
        $data = $this->buildData();

        if ($this->message && $this->message->exists) {
            $this->message->update($data);
            session()->flash('success', 'Broadcast draft updated successfully.');
        } else {
            $data['created_by'] = auth()->id();
            $this->message = Message::create($data);
            session()->flash('success', 'Broadcast created successfully.');
        }

        $this->redirect(route('messages.index'));
    }

    public function sendNow(): void
    {
        if ($this->message && $this->message->exists) {
            $this->authorize('update', $this->message);
        } else {
            $this->authorize('create', Message::class);
        }

        $this->validate();
        $data = $this->buildData();
        $data['status'] = 'sending';

        if ($this->message && $this->message->exists) {
            $this->message->update($data);
            $msg = $this->message;
        } else {
            $data['created_by'] = auth()->id();
            $msg = Message::create($data);
            $this->message = $msg;
        }

        $this->authorize('send', $msg);

        SendMessageJob::dispatch($msg);

        session()->flash('success', 'Broadcast sent successfully to members!');
        $this->redirect(route('messages.index'));
    }

    public function render()
    {
        $user = auth()->user();
        $groupsQuery = Group::query()->where('is_active', true)->orderBy('name');

        // If user is a leader without church-wide permission, restrict to groups they lead
        if (!$this->canBroadcastToChurch && $user && $user->person_id) {
            $groupsQuery->where(function ($q) use ($user) {
                $q->where('leader_id', $user->person_id)
                  ->orWhereHas('members', fn($mq) => $mq->where('people.id', $user->person_id)->where('group_members.role', 'leader'));
            });
        }

        $groups = $groupsQuery->get();
        $teams = Team::orderBy('name')->get();

        return view('livewire.messages.form', [
            'groups' => $groups,
            'teams' => $teams,
            'matchingCount' => $this->matchingPeopleCount,
        ])->layout('layouts.app', ['header' => 'New Church Broadcast']);
    }
}
