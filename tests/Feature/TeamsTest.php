<?php

use App\Models\Person;
use App\Models\Team;
use App\Models\TeamPosition;
use App\Models\User;
use Livewire\Livewire;

test('teams index screen can be rendered by authenticated user', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $this->get('/teams')->assertStatus(200);
});

test('admin or staff can create team, position, and attach member', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create(['first_name' => 'John', 'last_name' => 'Worship']);

    $this->actingAs($admin);

    Livewire::test('teams.form')
        ->set('name', 'Worship Ministry')
        ->set('description', 'Music and audio team')
        ->set('leader_id', $person->id)
        ->call('save')
        ->assertRedirect('/teams');

    $team = Team::where('name', 'Worship Ministry')->first();
    expect($team)->not()->toBeNull()
        ->and($team->leader_id)->toBe($person->id);

    // Test adding position and attaching member
    Livewire::test('teams.form', ['team' => $team])
        ->set('positionName', 'Vocalist')
        ->set('slotsPerService', 2)
        ->call('addPosition')
        ->assertHasNoErrors();

    $position = TeamPosition::where('team_id', $team->id)->where('name', 'Vocalist')->first();
    expect($position)->not()->toBeNull();

    Livewire::test('teams.form', ['team' => $team])
        ->set('selectedPersonId', $person->id)
        ->set('selectedPositionId', $position->id)
        ->call('attachMember')
        ->assertHasNoErrors();

    expect($team->members()->count())->toBe(1);
});

test('admin can delete a team using confirmation modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $team = Team::create(['name' => 'Temporary Team']);

    $this->actingAs($admin);

    Livewire::test('teams.index')
        ->call('confirmDelete', $team->id)
        ->assertSet('confirmingTeamDeletion', true)
        ->call('deleteTeam');

    expect(Team::find($team->id))->toBeNull();
});
