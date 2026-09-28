<?php

use App\Jobs\SendMessageJob;
use App\Livewire\Messages\Form as MessagesForm;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Messages\Show as MessagesShow;
use App\Models\Group;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Person;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('messages index screen can be rendered and filtered', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $msg1 = Message::create([
        'subject' => 'Email Newsletter',
        'body' => 'Welcome to church',
        'channel' => 'email',
        'status' => 'sent',
    ]);

    $msg2 = Message::create([
        'subject' => 'SMS Alert',
        'body' => 'Event delayed',
        'channel' => 'sms',
        'status' => 'draft',
    ]);

    Livewire::actingAs($user)
        ->test(MessagesIndex::class)
        ->assertStatus(200)
        ->assertSee('Email Newsletter')
        ->assertSee('SMS Alert')
        ->set('channel', 'email')
        ->assertSee('Email Newsletter')
        ->assertDontSee('SMS Alert')
        ->set('channel', '')
        ->set('status', 'draft')
        ->assertSee('SMS Alert')
        ->assertDontSee('Email Newsletter');
});

test('messages form creates message and live counts matching audience', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $person1 = Person::factory()->create([
        'email' => 'p1@example.com',
        'email_opt_in' => true,
        'membership_status' => 'member',
    ]);

    $person2 = Person::factory()->create([
        'email' => 'p2@example.com',
        'email_opt_in' => true,
        'membership_status' => 'visitor',
    ]);

    $person3 = Person::factory()->create([
        'phone' => '555-0199',
        'sms_opt_in' => true,
        'email_opt_in' => false,
        'membership_status' => 'member',
    ]);

    Livewire::actingAs($admin)
        ->test(MessagesForm::class)
        ->assertStatus(200)
        ->set('channel', 'email')
        ->assertSee('2 people')
        ->set('membership_status', 'member')
        ->assertSee('1 person')
        ->set('subject', 'Sunday Service')
        ->set('body', 'Join us at 10 AM')
        ->call('save')
        ->assertRedirect(route('messages.index'));

    $this->assertDatabaseHas('messages', [
        'subject' => 'Sunday Service',
        'body' => 'Join us at 10 AM',
        'channel' => 'email',
        'status' => 'draft',
    ]);
});

test('send now dispatches SendMessageJob', function () {
    Queue::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $message = Message::create([
        'subject' => 'Urgent Announcement',
        'body' => 'Notice text',
        'channel' => 'email',
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test(MessagesIndex::class)
        ->call('confirmSend', $message->id)
        ->assertSet('confirmingSend', true)
        ->call('sendNow')
        ->assertSet('confirmingSend', false);

    Queue::assertPushed(SendMessageJob::class, function ($job) use ($message) {
        return $job->message->id === $message->id;
    });

    $this->assertEquals('sending', $message->fresh()->status);
});

test('duplicate action duplicates message', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $original = Message::create([
        'subject' => 'Weekly Update',
        'body' => 'Updates here',
        'channel' => 'email',
        'status' => 'sent',
        'audience_filter' => ['membership_status' => 'member'],
    ]);

    Livewire::actingAs($admin)
        ->test(MessagesIndex::class)
        ->call('duplicate', $original->id);

    $this->assertDatabaseHas('messages', [
        'subject' => 'Copy of Weekly Update',
        'body' => 'Updates here',
        'status' => 'draft',
    ]);
});

test('SendMessageJob processes message recipients and updates status', function () {
    $person = Person::factory()->create([
        'email' => 'member@example.com',
        'email_opt_in' => true,
        'membership_status' => 'member',
    ]);

    $message = Message::create([
        'subject' => 'Job Test',
        'body' => 'Body text',
        'channel' => 'email',
        'status' => 'draft',
        'audience_filter' => ['membership_status' => 'member'],
    ]);

    $job = new SendMessageJob($message);
    $job->handle(app(\App\Contracts\MessageSender::class));

    $this->assertEquals('sent', $message->fresh()->status);
    $this->assertNotNull($message->fresh()->sent_at);

    $this->assertDatabaseHas('message_recipients', [
        'message_id' => $message->id,
        'person_id' => $person->id,
        'channel' => 'email',
        'address' => 'member@example.com',
        'status' => 'sent',
    ]);
});

test('show screen renders message details and paginated recipients', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create(['first_name' => 'Alice', 'last_name' => 'Smith']);

    $message = Message::create([
        'subject' => 'View Test Message',
        'body' => 'Sample content',
        'channel' => 'email',
        'status' => 'sent',
    ]);

    MessageRecipient::create([
        'message_id' => $message->id,
        'person_id' => $person->id,
        'channel' => 'email',
        'address' => 'alice@example.com',
        'status' => 'sent',
        'sent_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(MessagesShow::class, ['message' => $message])
        ->assertStatus(200)
        ->assertSee('View Test Message')
        ->assertSee('Alice Smith')
        ->assertSee('alice@example.com');
});

test('leader and viewer are blocked from creating or sending messages', function () {
    $leader = User::factory()->create(['role' => 'leader']);
    $viewer = User::factory()->create(['role' => 'viewer']);

    $message = Message::create([
        'subject' => 'Protected Msg',
        'body' => 'Body',
        'channel' => 'email',
        'status' => 'draft',
    ]);

    Livewire::actingAs($leader)
        ->test(MessagesForm::class)
        ->assertForbidden();

    Livewire::actingAs($viewer)
        ->test(MessagesForm::class)
        ->assertForbidden();

    Livewire::actingAs($leader)
        ->test(MessagesIndex::class)
        ->call('sendNow', $message->id)
        ->assertForbidden();
});
