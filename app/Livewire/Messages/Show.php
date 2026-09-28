<?php

namespace App\Livewire\Messages;

use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Message $message;

    public function mount(Message $message): void
    {
        $this->authorize('view', $message);
        $this->message = $message;
    }

    public function render()
    {
        $this->authorize('view', $this->message);

        $recipients = $this->message->recipients()
            ->with('person')
            ->latest()
            ->paginate(20);

        return view('livewire.messages.show', [
            'recipients' => $recipients,
        ])->layout('layouts.app');
    }
}
