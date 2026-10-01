<?php

use App\Livewire\Attendance\Index as AttendanceIndex;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Group;
use App\Models\Person;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;

test('attendance index screen can be rendered by authenticated user', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->get('/attendances')
        ->assertOk();
});

test('attendance list displays person full_name or dash if anonymous and resolves context column', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $service = Service::factory()->create(['name' => 'Sunday Service']);
    $group = Group::factory()->create(['name' => 'Youth Group']);

    $attPerson = Attendance::factory()->create([
        'person_id' => $person->id,
        'service_id' => $service->id,
        'attended_on' => '2026-09-01',
        'status' => 'present',
    ]);

    $attAnon = Attendance::factory()->create([
        'person_id' => null,
        'group_id' => $group->id,
        'attended_on' => '2026-09-02',
        'headcount' => 45,
        'status' => 'present',
    ]);

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->assertSee('John Doe')
        ->assertSee('Service: Sunday Service')
        ->assertSee('Group: Youth Group')
        ->assertSee('45');
});

test('attendance filters work correctly', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $service1 = Service::factory()->create(['name' => 'Service Alpha']);
    $service2 = Service::factory()->create(['name' => 'Service Beta']);

    $att1 = Attendance::factory()->create(['service_id' => $service1->id, 'attended_on' => '2026-09-10', 'status' => 'present']);
    $att2 = Attendance::factory()->create(['service_id' => $service2->id, 'attended_on' => '2026-09-15', 'status' => 'absent']);

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->set('serviceId', (string) $service1->id)
        ->assertSee('Service: Service Alpha')
        ->assertDontSee('Service: Service Beta')
        ->set('serviceId', '')
        ->set('statusFilter', 'absent')
        ->assertSee('Service: Service Beta')
        ->assertDontSee('Service: Service Alpha');
});

test('bulk mark present and mark absent actions update selected attendance records', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $att1 = Attendance::factory()->create(['status' => 'absent']);
    $att2 = Attendance::factory()->create(['status' => 'absent']);

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->set('selectedAttendanceIds', [(string) $att1->id, (string) $att2->id])
        ->call('bulkMarkStatus', 'present');

    expect($att1->fresh()->status)->toBe('present');
    expect($att2->fresh()->status)->toBe('present');

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->set('selectedAttendanceIds', [(string) $att1->id])
        ->call('bulkMarkStatus', 'absent');

    expect($att1->fresh()->status)->toBe('absent');
    expect($att2->fresh()->status)->toBe('present');
});

test('headcount field is required when person_id is null', function () {
    $user = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->call('openCreateModal')
        ->set('person_id', null)
        ->set('headcount', null)
        ->call('saveAttendance')
        ->assertHasErrors(['headcount' => 'required']);
});

test('headcount is not required when person_id is present', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $person = Person::factory()->create();
    $service = Service::factory()->create();

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->call('openCreateModal')
        ->set('person_id', $person->id)
        ->set('service_id', $service->id)
        ->set('headcount', null)
        ->set('attended_on', '2026-09-20')
        ->call('saveAttendance')
        ->assertHasNoErrors(['headcount']);

    $this->assertDatabaseHas('attendances', [
        'person_id' => $person->id,
        'service_id' => $service->id,
    ]);
});

test('can create anonymous headcount attendance when headcount is provided', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $service = Service::factory()->create();

    Livewire::actingAs($user)
        ->test(AttendanceIndex::class)
        ->call('openCreateModal')
        ->set('person_id', null)
        ->set('service_id', $service->id)
        ->set('headcount', 75)
        ->set('attended_on', '2026-09-25')
        ->call('saveAttendance')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('attendances', [
        'person_id' => null,
        'service_id' => $service->id,
        'headcount' => 75,
    ]);
});
