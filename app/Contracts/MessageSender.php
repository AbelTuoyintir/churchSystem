<?php

namespace App\Contracts;

use App\Models\Message;
use App\Models\MessageRecipient;

interface MessageSender
{
    /**
     * Send a message to a recipient.
     */
    public function send(Message $message, MessageRecipient $recipient): bool;
}
