<?php

use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use App\Policies\GroupPolicy;

test('group policy permits admin and staff for all actions', function () {
    $policy = new GroupPolicy;
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);
    $group = Group::factory()->create();

    foreach ([$admin, $staff] as $user) {
        expect($policy->viewAny($user))->toBeTrue();
        expect($policy->view($user, $group))->toBeTrue();
        expect($policy->create($user))->toBeTrue();
        expect($policy->update($user, $group))->toBeTrue();
        expect($policy->delete($user, $group))->toBeTrue();
    }
});

test('group leader can view and update assigned groups but not other groups or create or delete groups', function () {
    $policy = new GroupPolicy;
    $leaderPerson = Person::factory()->create();
    $leader = User::factory()->create(['role' => 'leader', 'person_id' => $leaderPerson->id]);

    $ownGroup = Group::factory()->create(['leader_id' => $leaderPerson->id]);
    $otherGroup = Group::factory()->create(['leader_id' => Person::factory()->create()->id]);

    expect($policy->viewAny($leader))->toBeTrue();
    expect($policy->view($leader, $ownGroup))->toBeTrue();
    expect($policy->view($leader, $otherGroup))->toBeFalse();
    expect($policy->create($leader))->toBeFalse();
    expect($policy->delete($leader, $ownGroup))->toBeFalse();

    // Leader can edit own group
    expect($policy->update($leader, $ownGroup))->toBeTrue();
    // Leader CANNOT edit other groups
    expect($policy->update($leader, $otherGroup))->toBeFalse();
});

test('a member or pastor with a leader pivot can manage that group only', function () {
    $policy = new GroupPolicy;
    $memberPerson = Person::factory()->create();
    $member = User::factory()->create(['role' => 'member', 'person_id' => $memberPerson->id]);
    $pastorPerson = Person::factory()->create();
    $pastor = User::factory()->create(['role' => 'pastor', 'person_id' => $pastorPerson->id]);
    $memberLedGroup = Group::factory()->create();
    $pastorLedGroup = Group::factory()->create();
    $otherGroup = Group::factory()->create();

    $memberLedGroup->members()->create([
        'person_id' => $memberPerson->id,
        'role' => 'leader',
        'joined_at' => now()->toDateString(),
    ]);
    $pastorLedGroup->members()->create([
        'person_id' => $pastorPerson->id,
        'role' => 'leader',
        'joined_at' => now()->toDateString(),
    ]);

    expect($member->isGroupLeader())->toBeTrue()
        ->and($policy->view($member, $memberLedGroup))->toBeTrue()
        ->and($policy->view($member, $otherGroup))->toBeFalse()
        ->and($pastor->isGroupLeader())->toBeTrue()
        ->and($policy->view($pastor, $pastorLedGroup))->toBeTrue();
});

test('a group leader pivot that has ended no longer grants group access', function () {
    $person = Person::factory()->create();
    $user = User::factory()->create(['role' => 'member', 'person_id' => $person->id]);
    $group = Group::factory()->create();
    $group->members()->create([
        'person_id' => $person->id,
        'role' => 'leader',
        'joined_at' => now()->subYear()->toDateString(),
        'left_at' => now()->subDay()->toDateString(),
    ]);

    expect($user->isGroupLeader())->toBeFalse()
        ->and($user->can('view', $group))->toBeFalse()
        ->and($user->can('update', $group))->toBeFalse();
});

test('viewer is read only', function () {
    $policy = new GroupPolicy;
    $viewer = User::factory()->create(['role' => 'viewer']);
    $group = Group::factory()->create();

    expect($policy->viewAny($viewer))->toBeTrue();
    expect($policy->view($viewer, $group))->toBeTrue();
    expect($policy->create($viewer))->toBeFalse();
    expect($policy->update($viewer, $group))->toBeFalse();
    expect($policy->delete($viewer, $group))->toBeFalse();
});
