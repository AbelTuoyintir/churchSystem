<?php

use App\Livewire\Groups\LeaderAttendance;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Person;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

test('attendance policy permits admin and staff for church-wide actions', function () {
    foreach (['admin', 'staff'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $attendance = Attendance::factory()->create();

        expect($user->can('viewAny', Attendance::class))->toBeTrue();
        expect($user->can('view', $attendance))->toBeTrue();
        expect($user->can('create', Attendance::class))->toBeTrue();
        expect($user->can('update', $attendance))->toBeTrue();
        expect($user->can('delete', $attendance))->toBeTrue();
    }
});

test('attendance policy restricts viewer to read-only', function () {
    $user = User::factory()->create(['role' => 'viewer']);
    $attendance = Attendance::factory()->create();

    expect($user->can('viewAny', Attendance::class))->toBeTrue();
    expect($user->can('view', $attendance))->toBeTrue();
    expect($user->can('create', Attendance::class))->toBeFalse();
    expect($user->can('update', $attendance))->toBeFalse();
    expect($user->can('delete', $attendance))->toBeFalse();
});

test('group leader can manage only their group meeting attendance', function () {
    $person = Person::factory()->create();
    $user = User::factory()->create(['role' => 'member', 'person_id' => $person->id]);
    $group = Group::factory()->create(['leader_id' => $person->id]);
    $otherGroup = Group::factory()->create();
    $ownAttendance = Attendance::factory()->create([
        'group_id' => $group->id,
        'service_id' => null,
        'event_id' => null,
    ]);
    $otherAttendance = Attendance::factory()->create([
        'group_id' => $otherGroup->id,
        'service_id' => null,
        'event_id' => null,
    ]);
    $service = Service::factory()->create();
    $serviceAttendance = Attendance::factory()->create([
        'group_id' => $group->id,
        'service_id' => $service->id,
        'event_id' => null,
    ]);

    expect($user->can('viewAny', Attendance::class))->toBeFalse()
        ->and($user->can('create', Attendance::class))->toBeFalse()
        ->and($user->can('view', $ownAttendance))->toBeTrue()
        ->and($user->can('update', $ownAttendance))->toBeTrue()
        ->and($user->can('view', $otherAttendance))->toBeFalse()
        ->and($user->can('update', $serviceAttendance))->toBeFalse();
});

test('group attendance recording is restricted to the active roster', function () {
    $leaderPerson = Person::factory()->create();
    $user = User::factory()->create(['role' => 'member', 'person_id' => $leaderPerson->id]);
    $group = Group::factory()->create(['leader_id' => $leaderPerson->id]);
    $member = Person::factory()->create();
    $outsider = Person::factory()->create();
    $group->members()->create([
        'person_id' => $member->id,
        'role' => 'member',
        'joined_at' => now()->toDateString(),
    ]);

    Livewire::actingAs($user)
        ->test(LeaderAttendance::class, ['groupId' => $group->id])
        ->set('statusByPerson.'.$member->id, 'absent')
        ->call('saveAttendance')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('attendances', [
        'group_id' => $group->id,
        'person_id' => $member->id,
        'status' => 'absent',
    ]);

    Livewire::actingAs($user)
        ->test(LeaderAttendance::class, ['groupId' => $group->id])
        ->set('statusByPerson.'.$outsider->id, 'present')
        ->call('saveAttendance')
        ->assertForbidden();
});
