<?php

use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Group;
use App\Models\Message;
use App\Models\Person;
use App\Models\Pledge;
use App\Models\User;
use App\Services\PushNotificationService;
use Livewire\Livewire;

test('member cannot access admin financial routes', function () {
    $member = User::factory()->create(['role' => 'member']);
    $fund = Fund::create(['name' => 'General Fund']);

    $this->actingAs($member)->get('/funds')->assertForbidden();
    $this->actingAs($member)->get('/contributions')->assertForbidden();
    $this->actingAs($member)->get('/contributions/create')->assertForbidden();
    $this->actingAs($member)->get('/pledges')->assertForbidden();
});

test('member can access member portal and online giving pages', function () {
    $person = Person::factory()->create();
    $member = User::factory()->create(['role' => 'member', 'person_id' => $person->id]);
    Fund::create(['name' => 'General Fund', 'is_active' => true]);

    $this->actingAs($member)->get('/member-portal')->assertOk();
    $this->actingAs($member)->get('/giving/pay')->assertOk();
    $this->actingAs($member)->get('/giving/my-contributions')->assertOk();
});

test('member can initiate paystack giving payment', function () {
    $fund = Fund::create(['name' => 'Tithe & Offering Fund', 'is_active' => true]);
    $person = Person::factory()->create(['email' => 'member@example.com']);
    $member = User::factory()->create(['role' => 'member', 'person_id' => $person->id, 'email' => 'member@example.com']);

    $response = $this->actingAs($member)->post('/giving/initiate', [
        'amount' => 150.00,
        'payment_type' => 'tithe',
        'fund_id' => $fund->id,
        'email' => 'member@example.com',
        'donor_name' => 'Faithful Member',
        'notes' => 'Monthly tithe',
    ]);

    $response->assertRedirect();
});

test('paystack callback records contribution and displays receipt', function () {
    $fund = Fund::create(['name' => 'Building Fund', 'is_active' => true]);
    $person = Person::factory()->create();
    $member = User::factory()->create(['role' => 'member', 'person_id' => $person->id]);
    $reference = 'CHMS_TEST_' . uniqid();

    session()->put('pending_payment_' . $reference, [
        'amount' => 200.00,
        'email' => $member->email,
        'metadata' => [
            'payment_type' => 'offering',
            'fund_id' => $fund->id,
            'person_id' => $person->id,
            'notes' => 'Sunday Offering',
        ],
    ]);

    $callbackResponse = $this->actingAs($member)->get('/giving/callback?reference=' . $reference);
    $callbackResponse->assertRedirect(route('payment.receipt', $reference));

    $contribution = Contribution::where('reference', $reference)->first();
    expect($contribution)->not->toBeNull();
    expect((float) $contribution->amount)->toBe(200.00);
    expect($contribution->method)->toBe('paystack');

    // Receipt page viewable
    $receiptResponse = $this->actingAs($member)->get("/giving/receipt/{$reference}");
    $receiptResponse->assertOk()->assertSee('Payment Successful');
});

test('pastor, admin, and group leader can broadcast messages', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pastor = User::factory()->create(['role' => 'pastor']);

    $leaderPerson = Person::factory()->create();
    $leaderUser = User::factory()->create(['role' => 'leader', 'person_id' => $leaderPerson->id]);
    $group = Group::create([
        'name' => 'Youth Fellowship',
        'type' => 'ministry',
        'leader_id' => $leaderPerson->id,
        'is_active' => true,
    ]);

    // Admin can create broadcast
    $this->actingAs($admin)->get('/messages/create')->assertOk();

    // Pastor can create broadcast
    $this->actingAs($pastor)->get('/messages/create')->assertOk();

    // Group leader can create broadcast
    $this->actingAs($leaderUser)->get('/messages/create')->assertOk();

    // Member cannot create broadcast
    $member = User::factory()->create(['role' => 'member']);
    $this->actingAs($member)->get('/messages/create')->assertForbidden();
});

test('user can subscribe to push notifications', function () {
    $user = User::factory()->create(['role' => 'member']);

    $response = $this->actingAs($user)->postJson('/push/subscribe', [
        'endpoint' => 'https://fcm.googleapis.com/fcm/send/sample-token-' . uniqid(),
        'keys' => [
            'p256dh' => 'sample-p256dh-key',
            'auth' => 'sample-auth-key',
        ],
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('push_subscriptions', [
        'user_id' => $user->id,
    ]);
});
