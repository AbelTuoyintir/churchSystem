<?php

use App\Livewire\Dashboard\AttendanceThisWeek;
use App\Models\Attendance;
use App\Models\Person;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

test('attendance this week widget calculates total headcount per service for current week', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $service1 = Service::factory()->create(['name' => 'First Service', 'is_active' => true]);
    $service2 = Service::factory()->create(['name' => 'Second Service', 'is_active' => true]);

    $startOfWeek = now()->startOfWeek()->toDateString();
    $lastWeek = now()->subWeek()->toDateString();

    $person1 = Person::factory()->create();
    $person2 = Person::factory()->create();

    // Service 1: 2 individual attendances this week (counts as 1+1=2) + 1 anonymous attendance of 50 = 52 total
    Attendance::factory()->create(['service_id' => $service1->id, 'person_id' => $person1->id, 'attended_on' => $startOfWeek, 'headcount' => null]);
    Attendance::factory()->create(['service_id' => $service1->id, 'person_id' => $person2->id, 'attended_on' => $startOfWeek, 'headcount' => 1]);
    Attendance::factory()->create(['service_id' => $service1->id, 'person_id' => null, 'attended_on' => $startOfWeek, 'headcount' => 50]);

    // Service 1: 1 attendance LAST week (should NOT be counted in this week's total)
    Attendance::factory()->create(['service_id' => $service1->id, 'person_id' => null, 'attended_on' => $lastWeek, 'headcount' => 100]);

    // Service 2: 1 anonymous attendance of 30 this week
    Attendance::factory()->create(['service_id' => $service2->id, 'person_id' => null, 'attended_on' => $startOfWeek, 'headcount' => 30]);

    Livewire::actingAs($user)
        ->test(AttendanceThisWeek::class)
        ->assertSee('First Service')
        ->assertSee('Second Service')
        ->assertSee('52')
        ->assertSee('30')
        ->assertSee('Total: 82');
});
