<?php

namespace App\Livewire\Messages;

use App\Jobs\SendMessageJob;
use App\Models\Group;
use App\Models\Message;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $channel = '';

    public string $status = '';

    public bool $confirmingSend = false;

    public ?int $messageIdBeingSent = null;

    #[Locked]
    public bool $leaderPortal = false;

    public function mount(): void
    {
        $this->leaderPortal = request()->routeIs('groups.lead.messages.*');
    }

    protected $queryString = [
        'channel' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingChannel(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function confirmSend(int $id): void
    {
        $message = $this->messagesQuery()->findOrFail($id);
        $this->authorize('send', $message);

        $this->messageIdBeingSent = $id;
        $this->confirmingSend = true;
    }

    public function sendNow(?int $id = null): void
    {
        $messageId = $id ?? $this->messageIdBeingSent;
        if (! $messageId) {
            return;
        }

        $message = $this->messagesQuery()->findOrFail($messageId);
        $this->authorize('send', $message);

        $message->update(['status' => 'sending']);

        SendMessageJob::dispatch($message);

        $this->confirmingSend = false;
        $this->messageIdBeingSent = null;

        session()->flash('success', 'Message queued for sending.');
    }

    public function duplicate(int $id): void
    {
        $this->authorize('create', Message::class);

        $original = $this->messagesQuery()->findOrFail($id);
        $this->authorize('view', $original);

        $duplicate = Message::create([
            'subject' => $original->subject ? 'Copy of '.$original->subject : 'Copy of Message',
            'body' => $original->body,
            'channel' => $original->channel,
            'status' => 'draft',
            'scheduled_at' => null,
            'sent_at' => null,
            'audience_filter' => $original->audience_filter,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Message duplicated.');
        $route = $this->leaderPortal ? 'groups.lead.messages.edit' : 'messages.edit';
        $this->redirect(route($route, $duplicate));
    }

    public function render()
    {
        $this->authorize('viewAny', Message::class);

        $user = auth()->user();
        $messagesQuery = $this->messagesQuery()
            ->withCount('recipients')
            ->when($this->channel !== '', fn ($q) => $q->where('channel', $this->channel))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status));

        $messages = $messagesQuery->latest()->paginate(15);

        return view('livewire.messages.index', [
            'messages' => $messages,
            'leaderPortal' => $this->leaderPortal,
        ])->layout('layouts.app');
    }

    private function messagesQuery()
    {
        $user = auth()->user();
        $query = Message::query();

        if ($this->leaderPortal || ($user->isGroupLeader() && ! $user->isStaff())) {
            $query->where('created_by', $user->id)
                ->where('audience_filter->target_audience', 'group')
                ->whereIn('audience_filter->group_id', Group::query()->ledBy($user)->select('id'));
        }

        return $query;
    }
}
