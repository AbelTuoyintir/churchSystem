<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Models\User;
use App\Services\CustomerPasswordService;
use Illuminate\Console\Command;

class GenerateCustomerPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:generate-password
                            {identifier : The Email, Phone, Person ID, or User ID of the customer}
                            {--channel=auto : Delivery channel: auto, email, sms, or both}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a unique password for a customer/user/person and send via email or sms';

    /**
     * Execute the console command.
     */
    public function handle(CustomerPasswordService $service): int
    {
        $identifier = $this->argument('identifier');
        $channel = strtolower($this->option('channel') ?? 'auto');

        if (!in_array($channel, ['auto', 'email', 'sms', 'both'])) {
            $this->error("Invalid channel option '{$channel}'. Allowed: auto, email, sms, both.");
            return Command::FAILURE;
        }

        // 1. Check if numeric ID matches a Person or User
        $person = null;
        $user = null;

        if (is_numeric($identifier)) {
            $user = User::find($identifier);
            if (!$user) {
                $person = Person::find($identifier);
            }
        }

        // 2. Check by email
        if (!$user && !$person && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
            if (!$user) {
                $person = Person::where('email', $identifier)->first();
            }
        }

        // 3. Check by phone or string email/name
        if (!$user && !$person) {
            $person = Person::where('phone', $identifier)
                ->orWhere('email', $identifier)
                ->orWhere('first_name', 'like', "%{$identifier}%")
                ->orWhere('last_name', 'like', "%{$identifier}%")
                ->first();

            if (!$person) {
                $user = User::where('email', $identifier)->first();
            }
        }

        if (!$user && !$person) {
            $this->error("No customer, user, or person found matching '{$identifier}'.");
            return Command::FAILURE;
        }

        if ($person) {
            $result = $service->generateAndSendForPerson($person, $channel);
        } else {
            $result = $service->generateAndSendForUser($user, $channel);
        }

        $this->info("----------------------------------------");
        $this->info("Customer Password Successfully Generated");
        $this->info("----------------------------------------");
        $this->line("Customer/User Name: " . ($result['person'] ? $result['person']->full_name : $result['user']->name));
        $this->line("Email:             " . $result['user']->email);
        $this->line("Generated Password: " . $result['password']);
        $this->line("Channels Sent:      " . implode(', ', $result['sent_channels'] ?: ['None']));
        $this->info($result['message']);

        return Command::SUCCESS;
    }
}
