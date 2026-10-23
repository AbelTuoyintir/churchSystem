<?php

namespace App\Services;

use App\Contracts\MessageSender;
use App\Mail\BroadcastEmail;
use App\Models\Message;
use App\Models\MessageRecipient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ChurchMessageSender implements MessageSender
{
    /**
     * Send a message to a recipient via Email or SMS.
     */
    public function send(Message $message, MessageRecipient $recipient): bool
    {
        try {
            $person = $recipient->person;
            if (!$person) {
                return false;
            }

            if ($recipient->channel === 'email') {
                return $this->sendEmail($message, $recipient);
            }

            if ($recipient->channel === 'sms') {
                return $this->sendSms($message, $recipient);
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('Broadcast send error: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'recipient_id' => $recipient->id,
            ]);
            $recipient->update(['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function sendEmail(Message $message, MessageRecipient $recipient): bool
    {
        if (empty($recipient->address) || !filter_var($recipient->address, FILTER_VALIDATE_EMAIL)) {
            $recipient->update(['error' => 'Invalid email address']);
            return false;
        }

        Mail::to($recipient->address)->send(new BroadcastEmail($message, $recipient->person));
        return true;
    }

    protected function sendSms(Message $message, MessageRecipient $recipient): bool
    {
        if (empty($recipient->address)) {
            $recipient->update(['error' => 'Phone number missing']);
            return false;
        }

        $smsApiKey = env('SMS_API_KEY');
        $smsSenderId = env('SMS_SENDER_ID', 'CHURCH');
        $smsEndpoint = env('SMS_API_ENDPOINT');

        // If real SMS gateway credentials are provided in .env, call the gateway
        if (!empty($smsApiKey) && !empty($smsEndpoint)) {
            try {
                $response = Http::timeout(10)->post($smsEndpoint, [
                    'api_key' => $smsApiKey,
                    'to' => $recipient->address,
                    'from' => $smsSenderId,
                    'sms' => $message->body,
                ]);

                if ($response->successful()) {
                    return true;
                }

                $recipient->update(['error' => 'SMS gateway returned error: ' . $response->body()]);
                return false;
            } catch (\Throwable $e) {
                $recipient->update(['error' => 'SMS error: ' . $e->getMessage()]);
                return false;
            }
        }

        // Development/Default driver fallback: Log the SMS transmission
        Log::info("SMS Broadcast sent to {$recipient->address}: {$message->body}");
        return true;
    }
}
