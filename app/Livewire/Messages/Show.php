<?php

namespace App\Livewire\Messages;

use App\Models\Group;
use App\Models\Message;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Message $message;

    #[Locked]
    public bool $leaderPortal = false;

    public function mount(Message $message): void
    {
        $this->leaderPortal = request()->routeIs('groups.lead.messages.*');
        $this->ensureLeaderMessageAccess($message);
        $this->authorize('view', $message);
        $this->message = $message;
    }

    public function render()
    {
        $this->ensureLeaderMessageAccess($this->message);
        $this->authorize('view', $this->message);

        $recipients = $this->message->recipients()
            ->with('person')
            ->latest()
            ->paginate(20);

        return view('livewire.messages.show', [
            'recipients' => $recipients,
            'leaderPortal' => $this->leaderPortal,
        ])->layout('layouts.app');
    }

    private function ensureLeaderMessageAccess(Message $message): void
    {
        if (! $this->leaderPortal) {
            return;
        }

        abort_unless((int) $message->created_by === (int) auth()->id()
            && data_get($message->audience_filter, 'target_audience') === 'group'
            && Group::query()->ledBy(auth()->user())->whereKey(data_get($message->audience_filter, 'group_id'))->exists(), 404);
    }
}
