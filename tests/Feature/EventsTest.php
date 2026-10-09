<?php

use App\Models\Event;
use App\Models\User;
use Livewire\Livewire;

test('events index screen can be rendered by authenticated user', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $this->get('/events')->assertStatus(200);
});

test('admin or staff can create and edit an event', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test('events.form')
        ->set('title', 'Summer Church Picnic')
        ->set('starts_at', '2026-07-15T11:00')
        ->set('location', 'Central Park')
        ->set('category', 'Fellowship')
        ->call('save')
        ->assertRedirect('/events');

    $event = Event::where('title', 'Summer Church Picnic')->first();
    expect($event)->not()->toBeNull()
        ->and($event->location)->toBe('Central Park');

    Livewire::test('events.form', ['event' => $event])
        ->set('location', 'Community Park')
        ->call('save')
        ->assertRedirect('/events');

    expect($event->fresh()->location)->toBe('Community Park');
});

test('admin can delete an event', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->create(['title' => 'Old Event']);

    $this->actingAs($admin);

    Livewire::test('events.index')
        ->call('confirmDelete', $event->id)
        ->assertSet('confirmingEventDeletion', true)
        ->call('deleteEvent');

    expect(Event::find($event->id))->toBeNull();
});
