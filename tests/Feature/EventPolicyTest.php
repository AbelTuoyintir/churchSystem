<?php

use App\Models\Event;
use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use App\Policies\EventPolicy;

test('group events are visible only to group members and leaders', function () {
    $leaderPerson = Person::factory()->create();
    $leader = User::factory()->create(['role' => 'member', 'person_id' => $leaderPerson->id]);
    $memberPerson = Person::factory()->create();
    $member = User::factory()->create(['role' => 'member', 'person_id' => $memberPerson->id]);
    $outsider = User::factory()->create(['role' => 'member', 'person_id' => Person::factory()->create()->id]);
    $group = Group::factory()->create(['leader_id' => $leaderPerson->id]);
    $group->members()->create([
        'person_id' => $memberPerson->id,
        'role' => 'member',
        'joined_at' => now()->toDateString(),
    ]);
    $privateEvent = Event::query()->create([
        'group_id' => $group->id,
        'title' => 'Group study night',
        'starts_at' => now()->addDay(),
        'is_public' => false,
    ]);
    $publicEvent = Event::query()->create([
        'title' => 'Community gathering',
        'starts_at' => now()->addDays(2),
        'is_public' => true,
    ]);

    $policy = new EventPolicy;

    expect($policy->view($leader, $privateEvent))->toBeTrue()
        ->and($policy->view($member, $privateEvent))->toBeTrue()
        ->and($policy->view($outsider, $privateEvent))->toBeFalse()
        ->and($policy->view($outsider, $publicEvent))->toBeTrue()
        ->and($policy->update($leader, $privateEvent))->toBeTrue()
        ->and($policy->update($outsider, $privateEvent))->toBeFalse();
});
