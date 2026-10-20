<?php

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Contribution;
use App\Models\Fund;
use App\Models\Group;
use App\Models\Household;
use App\Models\Person;
use App\Models\Pledge;
use App\Models\Service;
use App\Models\Team;
use App\Models\TeamPosition;
use App\Models\User;
use Livewire\Livewire;

test('guest is redirected to login from member portal', function () {
    $this->get('/member-portal')->assertRedirect('/login');
});

test('member can view member portal with person details', function () {
    $person = Person::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Member',
        'membership_status' => 'member',
    ]);

    $user = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $household = Household::factory()->create(['name' => 'Member Household']);
    $household->people()->attach($person->id, ['role' => 'head']);

    $group = Group::factory()->create(['name' => 'Member Life Group']);
    $group->people()->attach($person->id, ['role' => 'member']);

    $service = Service::factory()->create(['name' => 'Sunday Service']);
    Attendance::factory()->create([
        'person_id' => $person->id,
        'service_id' => $service->id,
        'status' => 'present',
    ]);

    $team = Team::create(['name' => 'Worship Team']);
    $position = TeamPosition::create(['team_id' => $team->id, 'name' => 'Vocalist']);
    Assignment::create([
        'team_id' => $team->id,
        'position_id' => $position->id,
        'person_id' => $person->id,
        'service_id' => $service->id,
        'assigned_on' => now()->toDateString(),
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->assertStatus(200)
        ->assertSee('Welcome, John Member!')
        ->assertSee('Member Household')
        ->assertSee('Member Life Group')
        ->assertSee('Worship Team')
        ->assertSee('Sunday Service');
});

test('member portal handles unlinked member user account smoothly', function () {
    $user = User::factory()->create([
        'person_id' => null,
        'role' => 'member',
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->assertStatus(200)
        ->assertSee('Your user account is not linked to a member profile yet.');
});

test('dashboard redirects member role to member-portal', function () {
    $user = User::factory()->create(['role' => 'member']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/member-portal');
});

test('dashboard renders overview for admin role', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertStatus(200)
        ->assertSeeLivewire('dashboard.admin-overview')
        ->assertSeeLivewire('dashboard.attendance-this-week');
});

test('member can edit contact information from member portal', function () {
    $person = Person::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone' => '123-456-7890',
        'address_line1' => '123 Main St',
        'city' => 'Old Town',
        'state' => 'NY',
        'postal_code' => '10001',
    ]);

    $user = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->set('phone', '555-999-8888')
        ->set('address_line1', '456 New Hope Rd')
        ->set('city', 'New City')
        ->set('state', 'CA')
        ->set('postal_code', '90210')
        ->call('updateProfile')
        ->assertHasNoErrors()
        ->assertSee('Your contact information has been updated successfully!');

    $person->refresh();
    expect($person->phone)->toBe('555-999-8888')
        ->and($person->address_line1)->toBe('456 New Hope Rd')
        ->and($person->city)->toBe('New City')
        ->and($person->state)->toBe('CA')
        ->and($person->postal_code)->toBe('90210');
});

test('unlinked user cannot update profile in member portal', function () {
    $user = User::factory()->create([
        'person_id' => null,
        'role' => 'member',
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->set('phone', '555-000-1111')
        ->call('updateProfile')
        ->assertSee('No linked profile found.');
});

test('member can view personal contributions and pledge progress on member portal', function () {
    $person = Person::factory()->create([
        'first_name' => 'Sarah',
        'last_name' => 'Giver',
    ]);

    $user = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $fund = Fund::create([
        'name' => 'Mission Expansion Fund',
    ]);

    Contribution::create([
        'person_id' => $person->id,
        'fund_id' => $fund->id,
        'amount' => 500.00,
        'method' => 'online',
        'contributed_on' => now()->toDateString(),
        'note' => 'Mission pledge payment',
    ]);

    Pledge::create([
        'person_id' => $person->id,
        'fund_id' => $fund->id,
        'amount' => 1000.00,
        'frequency' => 'monthly',
        'start_date' => now()->startOfYear()->toDateString(),
        'end_date' => now()->endOfYear()->toDateString(),
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->assertStatus(200)
        ->assertSee('My Giving', false)
        ->assertSee('Mission Expansion Fund')
        ->assertSee('$500.00')
        ->assertSee('My Pledges', false)
        ->assertSee('Goal: $1,000.00')
        ->assertSee('Raised: $500.00 (50%)');
});
