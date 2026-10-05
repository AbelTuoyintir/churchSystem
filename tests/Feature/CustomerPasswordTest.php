<?php

use App\Livewire\People\Form;
use App\Livewire\People\Index;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Person;
use App\Models\User;
use App\Services\CustomerPasswordService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('CustomerPasswordService generates password and creates user and sends email for person without user', function () {
    Mail::fake();

    $person = Person::factory()->create([
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'email' => 'alice.smith@example.com',
    ]);

    $service = app(CustomerPasswordService::class);
    $result = $service->generateAndSendForPerson($person, 'email');

    expect($result['success'])->toBeTrue();
    expect($result['password'])->not->toBeEmpty();

    $user = User::where('person_id', $person->id)->first();
    expect($user)->not->toBeNull();
    expect($user->email)->toBe('alice.smith@example.com');
    expect(Hash::check($result['password'], $user->password))->toBeTrue();

    $this->assertDatabaseHas('messages', [
        'subject' => 'Your Account Login Password',
        'channel' => 'email',
    ]);

    $this->assertDatabaseHas('message_recipients', [
        'address' => 'alice.smith@example.com',
        'channel' => 'email',
        'status' => 'sent',
    ]);
});

test('CustomerPasswordService sends SMS when channel is sms or auto with phone only', function () {
    $person = Person::factory()->create([
        'first_name' => 'Bob',
        'last_name' => 'Jones',
        'email' => null,
        'phone' => '555-9876',
    ]);

    $service = app(CustomerPasswordService::class);
    $result = $service->generateAndSendForPerson($person, 'auto');

    expect($result['success'])->toBeTrue();
    expect($result['sent_channels'])->toContain('sms');

    $this->assertDatabaseHas('messages', [
        'subject' => 'Your Account Password',
        'channel' => 'sms',
    ]);

    $this->assertDatabaseHas('message_recipients', [
        'address' => '555-9876',
        'channel' => 'sms',
    ]);
});

test('artisan command customer:generate-password generates password by email or phone or ID', function () {
    Mail::fake();

    $person = Person::factory()->create([
        'email' => 'cmd.test@example.com',
        'phone' => '555-1122',
    ]);

    $this->artisan('customer:generate-password', [
        'identifier' => 'cmd.test@example.com',
        '--channel' => 'email',
    ])
    ->expectsOutputToContain('Customer Password Successfully Generated')
    ->assertExitCode(0);

    $user = User::where('email', 'cmd.test@example.com')->first();
    expect($user)->not->toBeNull();
});

test('admin or staff can generate password from people index component', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create(['email' => 'member.index@example.com']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->call('confirmGeneratePassword', $person->id)
        ->assertSet('confirmingPasswordGeneration', true)
        ->call('generatePassword')
        ->assertSet('confirmingPasswordGeneration', false)
        ->assertSee('Password for');

    $user = User::where('person_id', $person->id)->first();
    expect($user)->not->toBeNull();
});

test('admin or staff can generate password from person form component', function () {
    Mail::fake();

    $staff = User::factory()->create(['role' => 'staff']);
    $person = Person::factory()->create(['email' => 'member.form@example.com']);

    Livewire::actingAs($staff)
        ->test(Form::class, ['person' => $person])
        ->call('confirmGeneratePassword')
        ->assertSet('confirmingPasswordGeneration', true)
        ->call('generatePassword')
        ->assertSet('confirmingPasswordGeneration', false);

    $user = User::where('person_id', $person->id)->first();
    expect($user)->not->toBeNull();
});

test('leader and viewer are forbidden from generating password', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $person = Person::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('confirmGeneratePassword', $person->id)
            ->call('generatePassword')
            ->assertForbidden();
    }
});
