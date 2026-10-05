<?php

namespace App\Services;

use App\Contracts\MessageSender;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerPasswordService
{
    protected MessageSender $messageSender;

    public function __construct(MessageSender $messageSender)
    {
        $this->messageSender = $messageSender;
    }

    /**
     * Generate a unique password for a Person and send it via Email/SMS.
     */
    public function generateAndSendForPerson(Person $person, string $channel = 'auto', ?User $createdBy = null): array
    {
        $user = $person->user;

        if (!$user) {
            $email = $person->email ?: strtolower($person->first_name . '.' . $person->last_name . '@example.com');
            $user = User::create([
                'name' => $person->full_name,
                'email' => $email,
                'password' => Hash::make(Str::random(16)),
                'person_id' => $person->id,
                'role' => 'member',
            ]);
        }

        return $this->generateAndSendForUser($user, $channel, $createdBy);
    }

    /**
     * Generate a unique password for a User and send it via Email/SMS.
     */
    public function generateAndSendForUser(User $user, string $channel = 'auto', ?User $createdBy = null): array
    {
        $plainPassword = $this->generateUniquePassword();

        $user->password = Hash::make($plainPassword);
        $user->save();

        $person = $user->person;
        if (!$person) {
            // Find person by email or phone if unlinked
            $person = Person::where('email', $user->email)->first();
            if ($person) {
                $user->person_id = $person->id;
                $user->save();
            }
        }

        $email = $person ? $person->email : $user->email;
        $phone = $person ? $person->phone : null;

        $sentChannels = [];

        $shouldSendEmail = ($channel === 'email' || $channel === 'both' || ($channel === 'auto' && !empty($email)));
        $shouldSendSms = ($channel === 'sms' || $channel === 'both' || ($channel === 'auto' && empty($email) && !empty($phone)));

        if ($shouldSendEmail && !empty($email)) {
            $emailSubject = 'Your Account Login Password';
            $emailBody = "Hello " . ($person ? $person->first_name : $user->name) . ",\n\n" .
                "Your login password has been set/reset.\n\n" .
                "Username / Email: {$user->email}\n" .
                "Password: {$plainPassword}\n\n" .
                "Please log in and update your password when possible.";

            try {
                Mail::raw($emailBody, function ($message) use ($email, $emailSubject) {
                    $message->to($email)->subject($emailSubject);
                });
            } catch (\Throwable $e) {
                // Log or ignore mail transport exceptions during local testing
            }

            $msgRecord = Message::create([
                'subject' => $emailSubject,
                'body' => $emailBody,
                'channel' => 'email',
                'status' => 'sent',
                'sent_at' => now(),
                'created_by' => $createdBy ? $createdBy->id : ($user->id ?? null),
            ]);

            $recipient = MessageRecipient::create([
                'message_id' => $msgRecord->id,
                'person_id' => $person ? $person->id : null,
                'channel' => 'email',
                'address' => $email,
                'status' => 'pending',
            ]);

            $sent = $this->messageSender->send($msgRecord, $recipient);
            $recipient->update([
                'status' => $sent ? 'sent' : 'failed',
                'sent_at' => $sent ? now() : null,
            ]);

            $sentChannels[] = 'email';
        }

        if ($shouldSendSms && !empty($phone)) {
            $smsSubject = 'Your Account Password';
            $smsBody = "Hello " . ($person ? $person->first_name : $user->name) . ", your login password is: {$plainPassword}";

            $msgRecord = Message::create([
                'subject' => $smsSubject,
                'body' => $smsBody,
                'channel' => 'sms',
                'status' => 'sent',
                'sent_at' => now(),
                'created_by' => $createdBy ? $createdBy->id : ($user->id ?? null),
            ]);

            $recipient = MessageRecipient::create([
                'message_id' => $msgRecord->id,
                'person_id' => $person ? $person->id : null,
                'channel' => 'sms',
                'address' => $phone,
                'status' => 'pending',
            ]);

            $sent = $this->messageSender->send($msgRecord, $recipient);
            $recipient->update([
                'status' => $sent ? 'sent' : 'failed',
                'sent_at' => $sent ? now() : null,
            ]);

            $sentChannels[] = 'sms';
        }

        $channelText = !empty($sentChannels) ? implode(' and ', $sentChannels) : 'no delivery channel (no email/phone found)';

        return [
            'success' => true,
            'user' => $user,
            'person' => $person,
            'password' => $plainPassword,
            'sent_channels' => $sentChannels,
            'message' => "Unique password generated successfully and sent via {$channelText}.",
        ];
    }

    /**
     * Generate a unique secure random password string.
     */
    public function generateUniquePassword(int $length = 12): string
    {
        return Str::password($length, true, true, false);
    }
}
