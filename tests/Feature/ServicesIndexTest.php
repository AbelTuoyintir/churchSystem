<?php

use App\Livewire\Services\Form as ServicesForm;
use App\Livewire\Services\Index as ServicesIndex;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

test('services index screen can be rendered', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/services')
        ->assertOk();
});

test('services index component lists services and filters', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $service1 = Service::factory()->create(['name' => 'Morning Service', 'is_active' => true]);
    $service2 = Service::factory()->create(['name' => 'Evening Service', 'is_active' => false]);

    Livewire::actingAs($user)
        ->test(ServicesIndex::class)
        ->assertSee('Morning Service')
        ->assertSee('Evening Service')
        ->set('search', 'Morning')
        ->assertSee('Morning Service')
        ->assertDontSee('Evening Service');
});

test('service form can create a service', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(ServicesForm::class)
        ->set('name', 'Sunday Celebration')
        ->set('day_of_week', 0)
        ->set('start_time', '10:00')
        ->set('location', 'Sanctuary')
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect('/services');

    $this->assertDatabaseHas('services', [
        'name' => 'Sunday Celebration',
        'day_of_week' => 0,
        'location' => 'Sanctuary',
    ]);
});

test('service form can edit a service', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $service = Service::factory()->create(['name' => 'Old Name']);

    Livewire::actingAs($user)
        ->test(ServicesForm::class, ['service' => $service])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertRedirect('/services');

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Updated Name',
    ]);
});

test('can delete a service', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $service = Service::factory()->create();

    Livewire::actingAs($user)
        ->test(ServicesIndex::class)
        ->call('confirmDelete', $service->id)
        ->call('deleteService');

    $this->assertDatabaseMissing('services', [
        'id' => $service->id,
    ]);
});
