<?php

use App\Models\Household;
use App\Models\Person;
use App\Models\User;

test('person policy permits admin and staff for all actions', function () {
    foreach (['admin', 'staff'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $person = Person::factory()->create();

        expect($user->can('viewAny', Person::class))->toBeTrue();
        expect($user->can('view', $person))->toBeTrue();
        expect($user->can('create', Person::class))->toBeTrue();
        expect($user->can('update', $person))->toBeTrue();
        expect($user->can('delete', $person))->toBeTrue();
        expect($user->can('viewNotes', $person))->toBeTrue();
    }
});

test('person policy restricts leader and viewer from creating updating deleting and viewing notes', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $person = Person::factory()->create();

        expect($user->can('viewAny', Person::class))->toBeTrue();
        expect($user->can('view', $person))->toBeTrue();
        expect($user->can('create', Person::class))->toBeFalse();
        expect($user->can('update', $person))->toBeFalse();
        expect($user->can('delete', $person))->toBeFalse();
        expect($user->can('viewNotes', $person))->toBeFalse();
    }
});

test('household policy permits admin and staff for all actions', function () {
    foreach (['admin', 'staff'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $household = Household::factory()->create();

        expect($user->can('viewAny', Household::class))->toBeTrue();
        expect($user->can('view', $household))->toBeTrue();
        expect($user->can('create', Household::class))->toBeTrue();
        expect($user->can('update', $household))->toBeTrue();
        expect($user->can('delete', $household))->toBeTrue();
    }
});

test('household policy restricts leader and viewer from creating updating deleting', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $household = Household::factory()->create();

        expect($user->can('viewAny', Household::class))->toBeTrue();
        expect($user->can('view', $household))->toBeTrue();
        expect($user->can('create', Household::class))->toBeFalse();
        expect($user->can('update', $household))->toBeFalse();
        expect($user->can('delete', $household))->toBeFalse();
    }
});
