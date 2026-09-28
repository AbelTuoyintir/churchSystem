<?php

namespace App\Services;

use App\Contracts\MessageSender;
use App\Models\Message;
use App\Models\MessageRecipient;

class NullMessageSender implements MessageSender
{
    /**
     * Send a message to a recipient.
     *
     * TODO: Integrate real email/SMS provider (e.g. Mailgun, Twilio, Amazon SES).
     */
    public function send(Message $message, MessageRecipient $recipient): bool
    {
        // Placeholder implementation for sending message via third-party provider
        return true;
    }
}
