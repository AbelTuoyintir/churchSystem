<?php

use App\Models\Service;
use App\Models\User;

test('service policy permits admin staff and leader for all actions', function () {
    foreach (['admin', 'staff', 'leader'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $service = Service::factory()->create();

        expect($user->can('viewAny', Service::class))->toBeTrue();
        expect($user->can('view', $service))->toBeTrue();
        expect($user->can('create', Service::class))->toBeTrue();
        expect($user->can('update', $service))->toBeTrue();
        expect($user->can('delete', $service))->toBeTrue();
    }
});

test('service policy restricts viewer to read-only', function () {
    $user = User::factory()->create(['role' => 'viewer']);
    $service = Service::factory()->create();

    expect($user->can('viewAny', Service::class))->toBeTrue();
    expect($user->can('view', $service))->toBeTrue();
    expect($user->can('create', Service::class))->toBeFalse();
    expect($user->can('update', $service))->toBeFalse();
    expect($user->can('delete', $service))->toBeFalse();
});
