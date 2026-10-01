<?php

use App\Models\Attendance;
use App\Models\User;

test('attendance policy permits admin staff and leader for all actions', function () {
    foreach (['admin', 'staff', 'leader'] as $role) {
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
