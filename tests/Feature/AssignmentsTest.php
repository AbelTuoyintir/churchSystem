<?php

use App\Models\Assignment;
use App\Models\Person;
use App\Models\Team;
use App\Models\TeamPosition;
use App\Models\User;
use Livewire\Livewire;

test('assignments index screen can be rendered by authenticated user', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $this->get('/assignments')->assertStatus(200);
});

test('admin or staff can create and update serving assignments', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create(['first_name' => 'Sara', 'last_name' => 'Helper']);
    $team = Team::create(['name' => 'Welcome Team']);
    $position = TeamPosition::create(['team_id' => $team->id, 'name' => 'Greeter']);

    $this->actingAs($admin);

    Livewire::test('assignments.form')
        ->set('team_id', $team->id)
        ->set('position_id', $position->id)
        ->set('person_id', $person->id)
        ->set('assigned_on', '2026-08-01')
        ->set('status', 'assigned')
        ->call('save')
        ->assertRedirect('/assignments');

    $assignment = Assignment::where('person_id', $person->id)->first();
    expect($assignment)->not()->toBeNull()
        ->and($assignment->status)->toBe('assigned');

    Livewire::test('assignments.index')
        ->call('updateStatus', $assignment->id, 'confirmed');

    expect($assignment->fresh()->status)->toBe('confirmed');
});

test('admin can delete serving assignment', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create();
    $team = Team::create(['name' => 'Tech Team']);
    $assignment = Assignment::create([
        'team_id' => $team->id,
        'person_id' => $person->id,
        'assigned_on' => '2026-08-01',
    ]);

    $this->actingAs($admin);

    Livewire::test('assignments.index')
        ->call('confirmDelete', $assignment->id)
        ->assertSet('confirmingAssignmentDeletion', true)
        ->call('deleteAssignment');

    expect(Assignment::find($assignment->id))->toBeNull();
});
