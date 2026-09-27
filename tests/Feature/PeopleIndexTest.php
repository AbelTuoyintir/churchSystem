<?php

use App\Livewire\People\Index;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('people index screen can be rendered by authenticated users', function () {
    $user = User::factory()->create(['role' => 'viewer']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertStatus(200);
});

test('people index screen lists people and supports searching and filtering', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $personA = Person::factory()->create([
        'first_name' => 'Alice',
        'last_name' => 'Smith',
        'email' => 'alice@example.com',
        'membership_status' => 'member',
        'is_active' => true,
    ]);

    $personB = Person::factory()->create([
        'first_name' => 'Bob',
        'last_name' => 'Jones',
        'email' => 'bob@example.com',
        'membership_status' => 'visitor',
        'is_active' => false,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('Smith')
        ->assertSee('Jones')
        ->set('search', 'Alice')
        ->assertSee('Smith')
        ->assertDontSee('Jones')
        ->set('search', '')
        ->set('membershipStatus', 'visitor')
        ->assertSee('Jones')
        ->assertDontSee('Smith')
        ->set('membershipStatus', '')
        ->set('isActive', '1')
        ->assertSee('Smith')
        ->assertDontSee('Jones');
});

test('admin or staff can delete a person using confirm modal', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('confirmDelete', $person->id)
        ->assertSet('confirmingDeletion', true)
        ->call('deletePerson')
        ->assertSet('confirmingDeletion', false);

    $this->assertSoftDeleted('people', ['id' => $person->id]);
});

test('leader and viewer are blocked from deleting a person', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $person = Person::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('confirmDelete', $person->id)
            ->call('deletePerson')
            ->assertForbidden();

        $this->assertDatabaseHas('people', ['id' => $person->id, 'deleted_at' => null]);
    }
});
