<?php

use App\Livewire\Households\Form;
use App\Models\Household;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('admin or staff can view create household form and create a household', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(Form::class)
        ->set('name', 'The Adams Family')
        ->set('phone', '555-9999')
        ->set('city', 'Metropolis')
        ->set('state', 'IL')
        ->call('save')
        ->assertRedirect(route('households.index'));

    $this->assertDatabaseHas('households', [
        'name' => 'The Adams Family',
        'city' => 'Metropolis',
        'state' => 'IL',
    ]);
});

test('admin or staff can edit a household and attach/detach members', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $household = Household::factory()->create(['name' => 'Original Name']);
    $person = Person::factory()->create();

    Livewire::actingAs($user)
        ->test(Form::class, ['household' => $household])
        ->assertSet('name', 'Original Name')
        ->set('name', 'Updated Household Name')
        ->call('save')
        ->assertRedirect(route('households.index'));

    $this->assertDatabaseHas('households', [
        'id' => $household->id,
        'name' => 'Updated Household Name',
    ]);

    // Test member attachment & detachment
    Livewire::actingAs($user)
        ->test(Form::class, ['household' => $household])
        ->set('selectedPersonId', $person->id)
        ->set('memberRole', 'spouse')
        ->call('attachMember');

    $this->assertDatabaseHas('household_members', [
        'household_id' => $household->id,
        'person_id' => $person->id,
        'role' => 'spouse',
    ]);

    Livewire::actingAs($user)
        ->test(Form::class, ['household' => $household])
        ->call('confirmDetachMember', $person->id)
        ->assertSet('confirmingMemberDetach', true)
        ->call('detachMember');

    $this->assertDatabaseMissing('household_members', [
        'household_id' => $household->id,
        'person_id' => $person->id,
    ]);
});

test('leader and viewer are forbidden from creating or updating a household', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        Livewire::actingAs($user)
            ->test(Form::class)
            ->assertForbidden();

        $household = Household::factory()->create();

        Livewire::actingAs($user)
            ->test(Form::class, ['household' => $household])
            ->set('name', 'Hacked Household')
            ->call('save')
            ->assertForbidden();
    }
});
