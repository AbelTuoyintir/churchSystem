<?php

use App\Livewire\Households\Index;
use App\Models\Household;
use App\Models\User;
use Livewire\Livewire;

test('households index screen can be rendered by authenticated users', function () {
    $user = User::factory()->create(['role' => 'viewer']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertStatus(200);
});

test('households index screen lists households and supports searching and filtering', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $hhA = Household::factory()->create(['name' => 'Alpha Family', 'state' => 'CA']);
    $hhB = Household::factory()->create(['name' => 'Beta Family', 'state' => 'NY']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('Alpha Family')
        ->assertSee('Beta Family')
        ->set('search', 'Alpha')
        ->assertSee('Alpha Family')
        ->assertDontSee('Beta Family')
        ->set('search', '')
        ->set('state', 'NY')
        ->assertSee('Beta Family')
        ->assertDontSee('Alpha Family');
});

test('admin or staff can delete a household using confirm modal', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $household = Household::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('confirmDelete', $household->id)
        ->assertSet('confirmingDeletion', true)
        ->call('deleteHousehold')
        ->assertSet('confirmingDeletion', false);

    $this->assertDatabaseMissing('households', ['id' => $household->id]);
});

test('leader and viewer are blocked from deleting a household', function () {
    foreach (['leader', 'viewer'] as $role) {
        $user = User::factory()->create(['role' => $role]);
        $household = Household::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('confirmDelete', $household->id)
            ->call('deleteHousehold')
            ->assertForbidden();

        $this->assertDatabaseHas('households', ['id' => $household->id]);
    }
});
