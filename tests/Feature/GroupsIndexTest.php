<?php

use App\Livewire\Groups\Index;
use App\Models\Group;
use App\Models\Person;
use App\Models\User;
use Livewire\Livewire;

test('groups index screen can be rendered by authenticated users', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertStatus(200);
});

test('groups index screen lists groups and supports searching and filtering', function () {
    $user = User::factory()->create(['role' => 'staff']);
    $leader = Person::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);

    $groupA = Group::factory()->create([
        'name' => 'Alpha Fellowship',
        'type' => 'small_group',
        'leader_id' => $leader->id,
        'is_active' => true,
    ]);

    $groupB = Group::factory()->create([
        'name' => 'Beta Choir',
        'type' => 'ministry',
        'is_active' => false,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('Alpha Fellowship')
        ->assertSee('Beta Choir')
        ->set('search', 'Alpha')
        ->assertSee('Alpha Fellowship')
        ->assertDontSee('Beta Choir')
        ->set('search', '')
        ->set('type', 'ministry')
        ->assertSee('Beta Choir')
        ->assertDontSee('Alpha Fellowship')
        ->set('type', '')
        ->set('isActive', '1')
        ->assertSee('Alpha Fellowship')
        ->assertDontSee('Beta Choir');
});

test('admin or staff can mark group inactive using confirmation modal', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $group = Group::factory()->create(['is_active' => true]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('confirmInactivate', $group->id)
        ->assertSet('confirmingInactivation', true)
        ->call('markInactive');

    $this->assertDatabaseHas('groups', [
        'id' => $group->id,
        'is_active' => false,
    ]);
});

test('viewer cannot mark group inactive', function () {
    $user = User::factory()->create(['role' => 'viewer']);
    $group = Group::factory()->create(['is_active' => true]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('confirmInactivate', $group->id)
        ->call('markInactive')
        ->assertForbidden();
});
