<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Message $messageModel;
    public Person $person;

    public function __construct(Message $messageModel, Person $person)
    {
        $this->messageModel = $messageModel;
        $this->person = $person;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->messageModel->subject ?: config('app.name', 'Church Announcement'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast',
        );
    }
}
