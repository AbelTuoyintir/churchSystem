<?php

use App\Livewire\People\Form;
use App\Models\Household;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('admin or staff can view create person form and create a person', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(Form::class)
        ->set('first_name', 'John')
        ->set('last_name', 'Doe')
        ->set('email', 'john.doe@example.com')
        ->set('phone', '555-1234')
        ->set('membership_status', 'member')
        ->set('address_line1', '123 Main St')
        ->set('city', 'Springfield')
        ->set('notes', 'Some pastoral note')
        ->call('save')
        ->assertRedirect(route('people.index'));

    $this->assertDatabaseHas('people', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'membership_status' => 'member',
        'city' => 'Springfield',
        'notes' => 'Some pastoral note',
    ]);
});

test('admin or staff can edit a person and attach/detach households', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $person = Person::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);
    $household = Household::factory()->create(['name' => 'Doe Household']);

    Livewire::actingAs($user)
        ->test(Form::class, ['person' => $person])
        ->assertSet('first_name', 'Jane')
        ->set('last_name', 'Smith')
        ->call('save')
        ->assertRedirect(route('people.index'));

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'last_name' => 'Smith',
    ]);

    // Test household relation panel attach & detach
    Livewire::actingAs($user)
        ->test(Form::class, ['person' => $person])
        ->set('selectedHouseholdId', $household->id)
        ->set('householdRole', 'head')
        ->call('attachHousehold');

    $this->assertDatabaseHas('household_members', [
        'household_id' => $household->id,
        'person_id' => $person->id,
        'role' => 'head',
    ]);

    Livewire::actingAs($user)
        ->test(Form::class, ['person' => $person])
        ->call('confirmDetachHousehold', $household->id)
        ->assertSet('confirmingHouseholdDetach', true)
        ->call('detachHousehold');

    $this->assertDatabaseMissing('household_members', [
        'household_id' => $household->id,
        'person_id' => $person->id,
    ]);
});

test('leader and viewer are forbidden from creating or updating a person', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        Livewire::actingAs($user)
            ->test(Form::class)
            ->assertForbidden();

        $person = Person::factory()->create();

        Livewire::actingAs($user)
            ->test(Form::class, ['person' => $person])
            ->set('first_name', 'Hacked')
            ->call('save')
            ->assertForbidden();
    }
});
