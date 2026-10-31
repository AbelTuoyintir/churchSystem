<?php

use App\Livewire\Admin\Events\Index as EventsIndex;
use App\Livewire\Admin\Exports\Index as ExportsIndex;
use App\Livewire\Admin\Settings\Index as SettingsIndex;
use App\Livewire\Admin\Assignments\Index as AssignmentsIndex;
use App\Livewire\Admin\Teams\Index as TeamsIndex;
use App\Models\AuditLog;
use App\Models\ChurchSetting;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Fund;
use App\Models\Person;
use App\Models\Team;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('only admins can open settings, audit log, and exports', function () {
    $member = User::factory()->create(['role' => 'member']);

    $this->actingAs($member)->get(route('admin.settings.index'))->assertForbidden();
    $this->get(route('admin.audit-log.index'))->assertForbidden();
    $this->get(route('admin.exports.index'))->assertForbidden();
});

test('settings save encrypted credentials without returning them to the interface', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $component = Livewire::actingAs($admin)->test(SettingsIndex::class)
        ->set('churchName', 'Grace Community')
        ->set('churchAddress', '12 Main Street')
        ->set('smtpPassword', 'smtp-secret-never-render')
        ->set('smsApiKey', 'sms-secret-never-render')
        ->call('saveSettings')
        ->assertHasNoErrors()
        ->assertSet('smtpPassword', '')
        ->assertSet('smsApiKey', '')
        ->assertDontSee('smtp-secret-never-render')
        ->assertDontSee('sms-secret-never-render');

    expect(ChurchSetting::query()->where('key', 'church_name')->first()?->value)->toBe('Grace Community');
    expect(ChurchSetting::query()->where('key', 'smtp_password')->first()?->value)->toBe('smtp-secret-never-render');

    $storedSecret = DB::table('church_settings')->where('key', 'smtp_password')->value('value');
    expect($storedSecret)->not->toBe('smtp-secret-never-render');
    expect($component->html())->not->toContain('sms-secret-never-render');
});

test('permission matrix changes affect role access without allowing admin lockout', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['role' => 'staff']);

    Livewire::actingAs($admin)->test(SettingsIndex::class)
        ->set('selectedRoleId', Role::query()->where('name', 'staff')->value('id'))
        ->set('selectedPermissions', ['people.view'])
        ->call('saveRolePermissions')
        ->assertHasNoErrors();

    expect($staff->fresh()->can('viewAny', Person::class))->toBeTrue();
    expect($staff->fresh()->can('create', Person::class))->toBeFalse();

    Livewire::actingAs($admin)->test(SettingsIndex::class)
        ->set('selectedRoleId', Role::query()->where('name', 'admin')->value('id'))
        ->call('saveRolePermissions')
        ->assertForbidden();
});

test('audit records cannot be updated or deleted', function () {
    $entry = AuditLog::query()->create([
        'action' => 'read',
        'description' => 'Viewed contribution records',
    ]);

    expect(fn () => $entry->update(['description' => 'Changed']))->toThrow(LogicException::class);
    expect(fn () => $entry->delete())->toThrow(LogicException::class);
    expect(AuditLog::query()->whereKey($entry->id)->exists())->toBeTrue();
});

test('export runs are admin-only and create an audit entry', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $member = User::factory()->create(['role' => 'member']);

    Livewire::actingAs($admin)->test(ExportsIndex::class)
        ->set('dataset', 'directory')
        ->call('export')
        ->assertFileDownloaded();

    $this->actingAs($member)->get(route('admin.exports.index'))->assertForbidden();

    expect(AuditLog::query()->where('action', 'export')->where('subject_type', 'directory')->exists())->toBeTrue();
});

test('sensitive financial reads are recorded with actor and request ip', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
        ->get(route('contributions.index'));

    $entry = AuditLog::query()
        ->where('action', 'read')
        ->where('subject_type', 'contributions')
        ->latest('id')
        ->first();

    expect($entry)->not->toBeNull();
    expect($entry->actor_id)->toBe($admin->id);
    expect($entry->ip_address)->toBe('192.0.2.10');
});

test('scheduling management is restricted to staff and admins', function () {
    $member = User::factory()->create(['role' => 'member']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($member)->get(route('assignments.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('assignments.index'))->assertOk();
});

test('admin navigation includes the new administrative tools', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.settings.index'))
        ->assertSee('Settings')
        ->assertSee('Audit Log')
        ->assertSee('Exports')
        ->assertSee('Scheduling');
});

test('staff can create church-wide events and teams', function () {
    $staff = User::factory()->create(['role' => 'staff']);

    Livewire::actingAs($staff)->test(EventsIndex::class)
        ->set('title', 'Community Gathering')
        ->set('startsAt', now()->addWeek()->format('Y-m-d\TH:i'))
        ->set('location', 'Main Hall')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::actingAs($staff)->test(TeamsIndex::class)
        ->set('name', 'Welcome Team')
        ->set('description', 'Hosts Sunday visitors')
        ->call('save')
        ->assertHasNoErrors();

    expect(Event::query()->where('title', 'Community Gathering')->exists())->toBeTrue();
    expect(Team::query()->where('name', 'Welcome Team')->exists())->toBeTrue();
});
