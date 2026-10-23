<?php

namespace App\Jobs;

use App\Contracts\MessageSender;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function handle(MessageSender $sender): void
    {
        $this->message->update(['status' => 'sending']);

        $filter = $this->message->audience_filter ?? [];
        $query = Person::query();

        if ($this->message->channel === 'email') {
            $query->where('email_opt_in', true)
                ->whereNotNull('email')
                ->where('email', '!=', '');
        } elseif ($this->message->channel === 'sms') {
            $query->where('sms_opt_in', true)
                ->whereNotNull('phone')
                ->where('phone', '!=', '');
        } elseif ($this->message->channel === 'both') {
            $query->where(function ($q) {
                $q->where(function ($eq) {
                    $eq->where('email_opt_in', true)->whereNotNull('email')->where('email', '!=', '');
                })->orWhere(function ($sq) {
                    $sq->where('sms_opt_in', true)->whereNotNull('phone')->where('phone', '!=', '');
                });
            });
        }

        if (!empty($filter['membership_status'])) {
            $query->where('membership_status', $filter['membership_status']);
        }

        if (!empty($filter['group_id'])) {
            $query->whereHas('groups', function ($q) use ($filter) {
                $q->where('groups.id', $filter['group_id']);
            });
        }

        if (!empty($filter['team_id'])) {
            $query->whereHas('teamMemberships', function ($q) use ($filter) {
                $q->where('team_id', $filter['team_id']);
            });
        }

        if (!empty($filter['email_opt_in'])) {
            $query->where('email_opt_in', true);
        }

        if (!empty($filter['sms_opt_in'])) {
            $query->where('sms_opt_in', true);
        }

        $people = $query->get();

        foreach ($people as $person) {
            $channelsToSend = [];
            if ($this->message->channel === 'email' && $person->email) {
                $channelsToSend[] = ['channel' => 'email', 'address' => $person->email];
            } elseif ($this->message->channel === 'sms' && $person->phone) {
                $channelsToSend[] = ['channel' => 'sms', 'address' => $person->phone];
            } elseif ($this->message->channel === 'both') {
                if ($person->email && $person->email_opt_in) {
                    $channelsToSend[] = ['channel' => 'email', 'address' => $person->email];
                }
                if ($person->phone && $person->sms_opt_in) {
                    $channelsToSend[] = ['channel' => 'sms', 'address' => $person->phone];
                }
            }

            foreach ($channelsToSend as $ch) {
                $recipient = MessageRecipient::firstOrCreate([
                    'message_id' => $this->message->id,
                    'person_id' => $person->id,
                    'channel' => $ch['channel'],
                ], [
                    'address' => $ch['address'],
                    'status' => 'pending',
                ]);

                $sent = $sender->send($this->message, $recipient);

                if ($sent) {
                    $recipient->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                } else {
                    $recipient->update([
                        'status' => 'failed',
                        'error' => $recipient->error ?? 'Failed to send message via provider.',
                    ]);
                }
            }
        }

        // Trigger Push Notification to church members
        try {
            $pushService = app(\App\Services\PushNotificationService::class);
            $pushTitle = $this->message->subject ?: 'New Church Announcement';
            $pushBody = \Illuminate\Support\Str::limit(strip_tags($this->message->body), 120);

            if (!empty($filter['group_id'])) {
                $group = \App\Models\Group::find($filter['group_id']);
                if ($group) {
                    $pushService->sendToGroup($group, $pushTitle, $pushBody, route('messages.show', $this->message));
                } else {
                    $pushService->sendToAll($pushTitle, $pushBody, route('messages.show', $this->message));
                }
            } else {
                $pushService->sendToAll($pushTitle, $pushBody, route('messages.show', $this->message));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::info('Push notification dispatch note: ' . $e->getMessage());
        }

        $this->message->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
