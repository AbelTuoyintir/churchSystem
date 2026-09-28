<?php

use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use App\Policies\GroupPolicy;

test('group policy permits admin and staff for all actions', function () {
    $policy = new GroupPolicy();
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

test('leader can view, viewAny, and update own group but not create or delete or update other groups', function () {
    $policy = new GroupPolicy();
    $leaderPerson = Person::factory()->create();
    $leader = User::factory()->create(['role' => 'leader', 'person_id' => $leaderPerson->id]);

    $ownGroup = Group::factory()->create(['leader_id' => $leaderPerson->id]);
    $otherGroup = Group::factory()->create(['leader_id' => Person::factory()->create()->id]);

    expect($policy->viewAny($leader))->toBeTrue();
    expect($policy->view($leader, $ownGroup))->toBeTrue();
    expect($policy->view($leader, $otherGroup))->toBeTrue();
    expect($policy->create($leader))->toBeFalse();
    expect($policy->delete($leader, $ownGroup))->toBeFalse();

    // Leader can edit own group
    expect($policy->update($leader, $ownGroup))->toBeTrue();
    // Leader CANNOT edit other groups
    expect($policy->update($leader, $otherGroup))->toBeFalse();
});

test('viewer is read only', function () {
    $policy = new GroupPolicy();
    $viewer = User::factory()->create(['role' => 'viewer']);
    $group = Group::factory()->create();

    expect($policy->viewAny($viewer))->toBeTrue();
    expect($policy->view($viewer, $group))->toBeTrue();
    expect($policy->create($viewer))->toBeFalse();
    expect($policy->update($viewer, $group))->toBeFalse();
    expect($policy->delete($viewer, $group))->toBeFalse();
});
