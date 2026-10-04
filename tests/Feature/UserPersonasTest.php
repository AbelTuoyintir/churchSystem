<?php

use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('guest persona can view welcome landing page with user personas section', function () {
    $this->get('/')
        ->assertStatus(200)
        ->assertSee('Tailored User Personas')
        ->assertSee('Administrator')
        ->assertSee('Group Leader')
        ->assertSee('Church Member')
        ->assertSee('Guest Visitor');
});

test('guest persona is redirected to login when accessing protected persona route', function () {
    $this->get('/personas')
        ->assertRedirect('/login');
});

test('admin persona can view personas page and access all sections including financial data', function () {
    $person = Person::factory()->create();
    $admin = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'admin',
    ]);

    $this->actingAs($admin);

    Livewire::test('personas.index')
        ->assertStatus(200)
        ->assertSee('System User Personas')
        ->assertSee('Administrator')
        ->assertSee('Church Member')
        ->assertSee('Leader of a Group')
        ->assertSee('Guest Visitor');

    $this->get('/people')->assertStatus(200);
    $this->get('/groups')->assertStatus(200);
    $this->get('/funds')->assertStatus(200);
    $this->get('/contributions')->assertStatus(200);
    $this->get('/pledges')->assertStatus(200);
});

test('member persona can view member portal and is forbidden from financial data', function () {
    $person = Person::factory()->create(['first_name' => 'Alice', 'last_name' => 'Member']);
    $member = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $this->actingAs($member);

    $this->get('/dashboard')->assertRedirect('/member-portal');

    Livewire::test('members.index')
        ->assertStatus(200)
        ->assertSee('Welcome, Alice Member!');

    $this->get('/funds')->assertStatus(403);
    $this->get('/contributions')->assertStatus(403);
    $this->get('/pledges')->assertStatus(403);
});

test('leader of a group persona can manage led group but cannot access financial data', function () {
    $leaderPerson = Person::factory()->create(['first_name' => 'Bob', 'last_name' => 'Leader']);
    $leader = User::factory()->create([
        'person_id' => $leaderPerson->id,
        'role' => 'leader',
    ]);

    $group = Group::factory()->create([
        'name' => 'Bob Small Group',
        'leader_id' => $leaderPerson->id,
    ]);

    $this->actingAs($leader);

    $this->get('/groups')->assertStatus(200)->assertSee('Bob Small Group');
    $this->get("/groups/{$group->id}/edit")->assertStatus(200);

    $this->get('/funds')->assertStatus(403);
    $this->get('/contributions')->assertStatus(403);
    $this->get('/pledges')->assertStatus(403);
});

test('persona index component allows switching roles and logging out for guest persona', function () {
    $person = Person::factory()->create();
    $user = User::factory()->create([
        'person_id' => $person->id,
        'role' => 'member',
    ]);

    $this->actingAs($user);

    // Switch to Admin
    Livewire::test('personas.index')
        ->call('switchToPersona', 'admin')
        ->assertRedirect('/dashboard');

    expect($user->fresh()->role)->toBe('admin');

    // Switch to Leader
    Livewire::test('personas.index')
        ->call('switchToPersona', 'leader')
        ->assertRedirect('/groups');

    expect($user->fresh()->role)->toBe('leader');
    expect(Group::where('leader_id', $person->id)->exists())->toBeTrue();

    // Switch to Member
    Livewire::test('personas.index')
        ->call('switchToPersona', 'member')
        ->assertRedirect('/member-portal');

    expect($user->fresh()->role)->toBe('member');

    // Switch to Guest
    Livewire::test('personas.index')
        ->call('switchToPersona', 'guest')
        ->assertRedirect('/');

    expect(auth()->check())->toBeFalse();
});
