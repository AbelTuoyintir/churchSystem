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

test('member can update their own profile contact details via member portal', function () {
    $person = Person::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone' => '111-222-3333',
        'email' => 'jane@example.com',
    ]);

    $user = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $this->actingAs($user);

    Livewire::test('members.index')
        ->call('editProfile')
        ->set('phone', '555-999-0000')
        ->set('address_line1', '123 New Grace Way')
        ->set('city', 'Springfield')
        ->set('state', 'IL')
        ->set('postal_code', '62701')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($person->fresh())
        ->phone->toBe('555-999-0000')
        ->address_line1->toBe('123 New Grace Way')
        ->city->toBe('Springfield');
});
