<?php

use App\Models\Person;
use App\Models\User;

test('admin sees full navigation menu including financial links', function () {
    $person = Person::factory()->create();
    $admin = User::factory()->create(['person_id' => $person->id, 'role' => 'admin']);

    $response = $this->actingAs($admin)->get('/people');

    $response->assertStatus(200);
    $response->assertSee('People');
    $response->assertSee('Households');
    $response->assertSee('Groups');
    $response->assertSee('Services');
    $response->assertSee('Attendance');
    $response->assertSee('Messages');
    $response->assertSee('Funds');
    $response->assertSee('Contributions');
    $response->assertSee('Pledges');
    $response->assertSee('My Portal');
    $response->assertSee('User Personas');
});

test('leader sees operational navigation menu but not financial links', function () {
    $person = Person::factory()->create();
    $leader = User::factory()->create(['person_id' => $person->id, 'role' => 'leader']);

    $response = $this->actingAs($leader)->get('/groups');

    $response->assertStatus(200);
    $response->assertSee('People');
    $response->assertSee('Households');
    $response->assertSee('Groups');
    $response->assertSee('Services');
    $response->assertSee('Attendance');
    $response->assertSee('Messages');
    $response->assertDontSee('Funds');
    $response->assertDontSee('Contributions');
    $response->assertDontSee('Pledges');
    $response->assertSee('My Portal');
    $response->assertSee('User Personas');
});

test('member sees member portal and user personas navigation', function () {
    $person = Person::factory()->create();
    $member = User::factory()->create(['person_id' => $person->id, 'role' => 'member']);

    $response = $this->actingAs($member)->get('/member-portal');

    $response->assertStatus(200);
    $response->assertSee('My Portal');
    $response->assertSee('User Personas');
});
