<?php

namespace App\Livewire\Messages;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $channel = '';
    public string $status = '';
    public bool $confirmingSend = false;
    public ?int $messageIdBeingSent = null;

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
        $message = Message::findOrFail($id);
        $this->authorize('send', $message);

        $this->messageIdBeingSent = $id;
        $this->confirmingSend = true;
    }

    public function sendNow(?int $id = null): void
    {
        $messageId = $id ?? $this->messageIdBeingSent;
        if (!$messageId) {
            return;
        }

        $message = Message::findOrFail($messageId);
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

        $original = Message::findOrFail($id);

        $duplicate = Message::create([
            'subject' => $original->subject ? 'Copy of ' . $original->subject : 'Copy of Message',
            'body' => $original->body,
            'channel' => $original->channel,
            'status' => 'draft',
            'scheduled_at' => null,
            'sent_at' => null,
            'audience_filter' => $original->audience_filter,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Message duplicated.');
        $this->redirect(route('messages.edit', $duplicate));
    }

    public function render()
    {
        $this->authorize('viewAny', Message::class);

        $messages = Message::query()
            ->withCount('recipients')
            ->when($this->channel !== '', fn($q) => $q->where('channel', $this->channel))
            ->when($this->status !== '', fn($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.messages.index', [
            'messages' => $messages,
        ])->layout('layouts.app');
    }
}
