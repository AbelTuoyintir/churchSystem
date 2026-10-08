<?php

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Household;
use App\Models\Person;
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

test('dashboard redirects admin role to people index', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/people');
});

test('member can edit own contact details from member portal', function () {
    $person = Person::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone' => '123-456-7890',
        'address_line1' => '123 Main St',
        'city' => 'Springfield',
    ]);

    $user = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->call('openEditProfileModal')
        ->assertSet('editingProfile', true)
        ->assertSet('phone', '123-456-7890')
        ->set('phone', '555-999-8888')
        ->set('preferred_name', 'Janie')
        ->set('address_line1', '456 Oak Ave')
        ->set('city', 'Metropolis')
        ->set('email_opt_in', true)
        ->set('sms_opt_in', true)
        ->call('updateProfile')
        ->assertSet('editingProfile', false);

    expect($person->fresh()->phone)->toBe('555-999-8888');
    expect($person->fresh()->preferred_name)->toBe('Janie');
    expect($person->fresh()->address_line1)->toBe('456 Oak Ave');
    expect($person->fresh()->city)->toBe('Metropolis');
    expect($person->fresh()->sms_opt_in)->toBeTrue();
});
