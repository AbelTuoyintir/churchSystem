<?php

use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Person;
use App\Models\User;
use App\Policies\ContributionPolicy;

test('leader receives 403 on index, create, and update for contributions', function () {
    $leader = User::factory()->create(['role' => 'leader']);
    $fund = Fund::create([
        'name' => 'General Fund',
        'is_active' => true,
    ]);
    $person = Person::factory()->create();
    $contribution = Contribution::create([
        'person_id' => $person->id,
        'fund_id' => $fund->id,
        'amount' => 100.00,
        'contributed_on' => now()->toDateString(),
        'method' => 'cash',
    ]);

    $this->actingAs($leader)
        ->get('/contributions')
        ->assertForbidden();

    $this->actingAs($leader)
        ->get('/contributions/create')
        ->assertForbidden();

    $this->actingAs($leader)
        ->get("/contributions/{$contribution->id}/edit")
        ->assertForbidden();
});

test('viewer receives 403 on index, create, and update for contributions', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);
    $fund = Fund::create([
        'name' => 'General Fund',
        'is_active' => true,
    ]);
    $person = Person::factory()->create();
    $contribution = Contribution::create([
        'person_id' => $person->id,
        'fund_id' => $fund->id,
        'amount' => 100.00,
        'contributed_on' => now()->toDateString(),
        'method' => 'cash',
    ]);

    $this->actingAs($viewer)
        ->get('/contributions')
        ->assertForbidden();

    $this->actingAs($viewer)
        ->get('/contributions/create')
        ->assertForbidden();

    $this->actingAs($viewer)
        ->get("/contributions/{$contribution->id}/edit")
        ->assertForbidden();
});

test('admin and staff can access index, create, and update for contributions', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);
    $fund = Fund::create([
        'name' => 'General Fund',
        'is_active' => true,
    ]);
    $person = Person::factory()->create();
    $contribution = Contribution::create([
        'person_id' => $person->id,
        'fund_id' => $fund->id,
        'amount' => 100.00,
        'contributed_on' => now()->toDateString(),
        'method' => 'cash',
    ]);

    foreach ([$admin, $staff] as $user) {
        $this->actingAs($user)
            ->get('/contributions')
            ->assertOk();

        $this->actingAs($user)
            ->get('/contributions/create')
            ->assertOk();

        $this->actingAs($user)
            ->get("/contributions/{$contribution->id}/edit")
            ->assertOk();
    }
});

test('contribution policy denies leader and viewer on all abilities', function () {
    $policy = new ContributionPolicy();
    $leader = User::factory()->create(['role' => 'leader']);
    $viewer = User::factory()->create(['role' => 'viewer']);
    $fund = Fund::create(['name' => 'General Fund']);
    $contribution = Contribution::create([
        'fund_id' => $fund->id,
        'amount' => 50.00,
        'contributed_on' => now()->toDateString(),
        'method' => 'cash',
    ]);

    foreach ([$leader, $viewer] as $user) {
        expect($policy->viewAny($user))->toBeFalse();
        expect($policy->view($user, $contribution))->toBeFalse();
        expect($policy->create($user))->toBeFalse();
        expect($policy->update($user, $contribution))->toBeFalse();
        expect($policy->delete($user, $contribution))->toBeFalse();
    }
});
