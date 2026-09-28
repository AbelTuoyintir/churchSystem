<?php

namespace App\Livewire\Messages;

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

    // Audience filter properties
    public string $membership_status = '';
    public string $group_id = '';
    public string $team_id = '';
    public bool $email_opt_in = false;
    public bool $sms_opt_in = false;

    public function mount(?Message $message = null): void
    {
        if ($message && $message->exists) {
            $this->authorize('update', $message);
            $this->message = $message;

            $this->subject = $message->subject ?? '';
            $this->body = $message->body ?? '';
            $this->channel = $message->channel ?? 'email';
            $this->scheduled_at = $message->scheduled_at ? $message->scheduled_at->format('Y-m-d\TH:i') : null;

            $filter = $message->audience_filter ?? [];
            $this->membership_status = $filter['membership_status'] ?? '';
            $this->group_id = isset($filter['group_id']) ? (string) $filter['group_id'] : '';
            $this->team_id = isset($filter['team_id']) ? (string) $filter['team_id'] : '';
            $this->email_opt_in = !empty($filter['email_opt_in']);
            $this->sms_opt_in = !empty($filter['sms_opt_in']);
        } else {
            $this->authorize('create', Message::class);
            $this->message = new Message();
        }
    }

    public function rules(): array
    {
        return [
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'channel' => ['required', 'in:email,sms'],
            'scheduled_at' => ['nullable', 'date'],
            'membership_status' => ['nullable', 'string'],
            'group_id' => ['nullable', 'string'],
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
        }

        if ($this->membership_status !== '') {
            $query->where('membership_status', $this->membership_status);
        }

        if ($this->group_id !== '') {
            $query->whereHas('groups', function ($q) {
                $q->where('groups.id', $this->group_id);
            });
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

    public function save(): void
    {
        if ($this->message && $this->message->exists) {
            $this->authorize('update', $this->message);
        } else {
            $this->authorize('create', Message::class);
        }

        $validated = $this->validate();

        $audienceFilter = [
            'membership_status' => $this->membership_status !== '' ? $this->membership_status : null,
            'group_id' => $this->group_id !== '' ? (int) $this->group_id : null,
            'team_id' => $this->team_id !== '' ? (int) $this->team_id : null,
            'email_opt_in' => $this->email_opt_in,
            'sms_opt_in' => $this->sms_opt_in,
        ];

        $status = 'draft';
        if (!empty($this->scheduled_at) && strtotime($this->scheduled_at) > time()) {
            $status = 'scheduled';
        }

        $data = [
            'subject' => $this->subject,
            'body' => $this->body,
            'channel' => $this->channel,
            'scheduled_at' => $this->scheduled_at ? $this->scheduled_at : null,
            'status' => $status,
            'audience_filter' => $audienceFilter,
        ];

        if ($this->message && $this->message->exists) {
            $this->message->update($data);
            session()->flash('success', 'Message updated successfully.');
        } else {
            $data['created_by'] = auth()->id();
            Message::create($data);
            session()->flash('success', 'Message created successfully.');
        }

        $this->redirect(route('messages.index'));
    }

    public function render()
    {
        $groups = Group::orderBy('name')->get();
        $teams = Team::orderBy('name')->get();

        return view('livewire.messages.form', [
            'groups' => $groups,
            'teams' => $teams,
            'matchingCount' => $this->matchingPeopleCount,
        ])->layout('layouts.app');
    }
}
