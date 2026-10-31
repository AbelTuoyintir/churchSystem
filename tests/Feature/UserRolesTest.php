<?php

use App\Livewire\Admin\UserRoles\Index;
use App\Models\User;
use Livewire\Livewire;

test('only admins can access user role management', function () {
    $member = User::factory()->create(['role' => 'member']);

    $this->actingAs($member)
        ->get(route('admin.user-roles.index'))
        ->assertForbidden();

    $this->get(route('members.portal'))
        ->assertDontSee('User Roles');
});

test('admins can see accounts and role options on the role management page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = User::factory()->create([
        'name' => 'Alex Member',
        'email' => 'alex@example.test',
        'role' => 'member',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.user-roles.index'))
        ->assertSee('User Role Management')
        ->assertSee('User Roles')
        ->assertSee('Alex Member')
        ->assertSee('alex@example.test')
        ->assertSee('Pastor')
        ->assertSee('Steward');
});

test('admins can search user accounts by name or email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    User::factory()->create([
        'name' => 'Alex Member',
        'email' => 'alex@example.test',
        'role' => 'member',
    ]);
    User::factory()->create([
        'name' => 'Sam Steward',
        'email' => 'sam@example.test',
        'role' => 'steward',
    ]);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set('search', 'alex@example.test')
        ->assertSee('alex@example.test')
        ->assertDontSee('sam@example.test');
});

test('admin can assign a role and the legacy role stays in sync', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = User::factory()->create(['role' => 'member']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set("selectedRoles.{$member->id}", 'pastor')
        ->call('updateRole', $member->id)
        ->assertHasNoErrors()
        ->assertDispatched('notify', type: 'success', message: 'User role updated successfully.');

    expect($member->fresh()->role)->toBe('pastor');
    expect($member->fresh()->hasRole('pastor'))->toBeTrue();
});

test('admin cannot assign an unsupported role', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = User::factory()->create(['role' => 'member']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set("selectedRoles.{$member->id}", 'super-admin')
        ->call('updateRole', $member->id)
        ->assertHasErrors(["selectedRoles.{$member->id}"])
        ->assertDispatched('notify', type: 'error', message: 'Unsupported role.');

    expect($member->fresh()->role)->toBe('member');
    expect($member->fresh()->hasRole('super-admin'))->toBeFalse();
});

test('admin cannot change their own role', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(Index::class)
        ->set("selectedRoles.{$admin->id}", 'member')
        ->call('updateRole', $admin->id)
        ->assertHasErrors(["selectedRoles.{$admin->id}"]);

    expect($admin->fresh()->role)->toBe('admin');
});
