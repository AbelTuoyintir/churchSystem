<?php

use App\Livewire\Groups\Form;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('admin or staff can view create group form and create a group', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $leader = Person::factory()->create();

    Livewire::actingAs($user)
        ->test(Form::class)
        ->set('name', 'Youth Ministry')
        ->set('type', 'ministry')
        ->set('leader_id', $leader->id)
        ->set('meeting_day', 'Friday')
        ->set('meeting_time', '18:00')
        ->call('save')
        ->assertRedirect(route('groups.index'));

    $this->assertDatabaseHas('groups', [
        'name' => 'Youth Ministry',
        'type' => 'ministry',
        'leader_id' => $leader->id,
        'meeting_day' => 'Friday',
    ]);
});

test('leader can edit own group and attach or detach members', function () {
    $leaderPerson = Person::factory()->create();
    $user = User::factory()->create(['role' => 'leader', 'person_id' => $leaderPerson->id]);
    $ownGroup = Group::factory()->create(['name' => 'Original Name', 'leader_id' => $leaderPerson->id]);
    $memberPerson = Person::factory()->create();

    Livewire::actingAs($user)
        ->test(Form::class, ['group' => $ownGroup])
        ->assertSet('name', 'Original Name')
        ->set('name', 'Leader Updated Group Name')
        ->call('save')
        ->assertRedirect(route('groups.index'));

    $this->assertDatabaseHas('groups', [
        'id' => $ownGroup->id,
        'name' => 'Leader Updated Group Name',
    ]);

    // Attach member
    Livewire::actingAs($user)
        ->test(Form::class, ['group' => $ownGroup])
        ->set('selectedPersonId', $memberPerson->id)
        ->set('memberRole', 'co_leader')
        ->set('joinedAt', '2026-01-01')
        ->call('attachMember');

    $groupMember = GroupMember::where('group_id', $ownGroup->id)
        ->where('person_id', $memberPerson->id)
        ->first();

    expect($groupMember)->not->toBeNull();
    expect($groupMember->role)->toBe('co_leader');
    expect($groupMember->left_at)->toBeNull();

    // Detach member sets left_at rather than deleting row
    Livewire::actingAs($user)
        ->test(Form::class, ['group' => $ownGroup])
        ->call('confirmDetachMember', $groupMember->id)
        ->assertSet('confirmingMemberDetach', true)
        ->call('detachMember');

    $groupMember->refresh();
    expect($groupMember->left_at)->not->toBeNull();
    $this->assertDatabaseHas('group_members', [
        'id' => $groupMember->id,
    ]);
});

test('leader cannot edit another leaders group', function () {
    $leaderPerson1 = Person::factory()->create();
    $leaderPerson2 = Person::factory()->create();

    $user = User::factory()->create(['role' => 'leader', 'person_id' => $leaderPerson1->id]);
    $otherGroup = Group::factory()->create(['leader_id' => $leaderPerson2->id]);

    Livewire::actingAs($user)
        ->test(Form::class, ['group' => $otherGroup])
        ->set('name', 'Hacked Group Name')
        ->call('save')
        ->assertForbidden();
});

test('viewer is forbidden from creating or updating a group', function () {
    $user = User::factory()->create(['role' => 'viewer']);
    $group = Group::factory()->create();

    Livewire::actingAs($user)
        ->test(Form::class)
        ->assertForbidden();

    Livewire::actingAs($user)
        ->test(Form::class, ['group' => $group])
        ->set('name', 'Viewer Attempt')
        ->call('save')
        ->assertForbidden();
});
