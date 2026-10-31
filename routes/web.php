<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HouseholdMemberController;
use App\Http\Controllers\MessageRecipientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamPositionController;
use App\Livewire\Admin\UserRoles\Index as UserRolesIndex;
use App\Livewire\Admin\Settings\Index as AdminSettingsIndex;
use App\Livewire\Admin\AuditLog\Index as AdminAuditLogIndex;
use App\Livewire\Admin\Exports\Index as AdminExportsIndex;
use App\Livewire\Admin\Assignments\Index as AdminAssignmentsIndex;
use App\Livewire\Admin\Events\Index as AdminEventsIndex;
use App\Livewire\Admin\Teams\Index as AdminTeamsIndex;
use App\Livewire\Attendance\Index as AttendanceIndex;
use App\Livewire\Contributions\Form as ContributionsForm;
use App\Livewire\Contributions\Index as ContributionsIndex;
use App\Livewire\Events\Index as EventsIndex;
use App\Livewire\Funds\Form as FundsForm;
use App\Livewire\Funds\Index as FundsIndex;
use App\Livewire\Giving\MyGiving as GivingMy;
use App\Livewire\Giving\Pay as GivingPay;
use App\Livewire\Groups\Browse as GroupsBrowse;
use App\Livewire\Groups\Form as GroupsForm;
use App\Livewire\Groups\Index as GroupsIndex;
use App\Livewire\Groups\LeaderAttendance as GroupsLeaderAttendance;
use App\Livewire\Groups\LeadEvents as GroupsLeadEvents;
use App\Livewire\Groups\LeadIndex as GroupsLeadIndex;
use App\Livewire\Groups\LeadPrayerRequests as GroupsLeadPrayerRequests;
use App\Livewire\Groups\LeadReports as GroupsLeadReports;
use App\Livewire\Groups\LeadTeams as GroupsLeadTeams;
use App\Livewire\Groups\MemberShow as GroupsMemberShow;
use App\Livewire\Households\Form as HouseholdsForm;
use App\Livewire\Households\Index as HouseholdsIndex;
use App\Livewire\Households\MyHousehold;
use App\Livewire\Members\Index as MembersIndex;
use App\Livewire\Messages\Form as MessagesForm;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Messages\Show as MessagesShow;
use App\Livewire\People\Form as PeopleForm;
use App\Livewire\People\Index as PeopleIndex;
use App\Livewire\Personas\Index as PersonasIndex;
use App\Livewire\PastoralCare\Index as PastoralCareIndex;
use App\Livewire\Reports\Index as ReportsIndex;
use App\Livewire\Pledges\Form as PledgesForm;
use App\Livewire\Pledges\Index as PledgesIndex;
use App\Livewire\PrayerRequests\Index as PrayerRequestsIndex;
use App\Livewire\Schedule\Index as ScheduleIndex;
use App\Livewire\Services\Form as ServicesForm;
use App\Livewire\Services\Index as ServicesIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isMember()) {
            return redirect()->route('members.portal');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::get('/member-portal', MembersIndex::class)->name('members.portal');
    Route::get('/my-household', MyHousehold::class)->name('members.household');
    Route::get('/community/groups', GroupsBrowse::class)->name('members.groups');
    Route::get('/community/groups/{group}', GroupsMemberShow::class)->name('members.groups.show');
    Route::get('/my-groups', GroupsLeadIndex::class)->name('groups.lead.index');
    Route::get('/my-groups/directory', PeopleIndex::class)->name('groups.lead.directory');
    Route::get('/my-groups/attendance/{groupId?}', GroupsLeaderAttendance::class)->name('groups.lead.attendance');
    Route::get('/my-groups/reports/{groupId?}', GroupsLeadReports::class)->name('groups.lead.reports');
    Route::get('/my-groups/events/{groupId?}', GroupsLeadEvents::class)->name('groups.lead.events');
    Route::get('/my-groups/prayer-requests/{groupId?}', GroupsLeadPrayerRequests::class)->name('groups.lead.prayer-requests');
    Route::get('/my-groups/teams', GroupsLeadTeams::class)->name('groups.lead.teams');
    Route::get('/my-groups/messages', MessagesIndex::class)->name('groups.lead.messages.index');
    Route::get('/my-groups/messages/create', MessagesForm::class)->name('groups.lead.messages.create');
    Route::get('/my-groups/messages/{message}/edit', MessagesForm::class)->name('groups.lead.messages.edit');
    Route::get('/my-groups/messages/{message}', MessagesShow::class)->name('groups.lead.messages.show');
    Route::get('/community/events', EventsIndex::class)->name('members.events');
    Route::get('/community/prayer-requests', PrayerRequestsIndex::class)->name('members.prayer-requests');
    Route::get('/prayer-requests', PrayerRequestsIndex::class)->name('members.prayer-requests');
Route::get('/pastoral-care', PastoralCareIndex::class)->name('pastoral-care.index');
Route::get('/pastoral-care/index', PastoralCareIndex::class)->name('pastoral.care.index');
Route::get('/reports', ReportsIndex::class)->name('reports');
Route::get('/reports/index', ReportsIndex::class)->name('reports.index');
    Route::get('/my-schedule', ScheduleIndex::class)->name('members.schedule');
    Route::get('/personas', PersonasIndex::class)->name('personas.index');
    Route::get('/admin/user-roles', UserRolesIndex::class)->name('admin.user-roles.index');
    Route::get('/admin/settings', AdminSettingsIndex::class)->name('admin.settings.index');
    Route::get('/admin/audit-log', AdminAuditLogIndex::class)->name('admin.audit-log.index');
    Route::get('/admin/exports', AdminExportsIndex::class)->name('admin.exports.index');
    Route::get('/admin/events', AdminEventsIndex::class)->name('admin.events.index');
    Route::get('/admin/teams', AdminTeamsIndex::class)->name('admin.teams.index');

    Route::get('/people', PeopleIndex::class)->name('people.index');
    Route::get('/people/create', PeopleForm::class)->name('people.create');
    Route::get('/people/{person}/edit', PeopleForm::class)->name('people.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/households', HouseholdsIndex::class)->name('households.index');
    Route::get('/households/create', HouseholdsForm::class)->name('households.create');
    Route::get('/households/{household}/edit', HouseholdsForm::class)->name('households.edit');

    Route::get('/groups', GroupsIndex::class)->name('groups.index');
    Route::get('/groups/create', GroupsForm::class)->name('groups.create');
    Route::get('/groups/{group}/edit', GroupsForm::class)->name('groups.edit');

    Route::get('/messages', MessagesIndex::class)->name('messages.index');
    Route::get('/messages/create', MessagesForm::class)->name('messages.create');
    Route::get('/messages/{message}/edit', MessagesForm::class)->name('messages.edit');
    Route::get('/messages/{message}', MessagesShow::class)->name('messages.show');

    Route::get('/services', ServicesIndex::class)->name('services.index');
    Route::get('/services/create', ServicesForm::class)->name('services.create');
    Route::get('/services/{service}/edit', ServicesForm::class)->name('services.edit');

    Route::get('/attendances', AttendanceIndex::class)->name('attendances.index');

    Route::get('/funds', FundsIndex::class)->name('funds.index');
    Route::get('/funds/create', FundsForm::class)->name('funds.create');
    Route::get('/funds/{fund}/edit', FundsForm::class)->name('funds.edit');

    Route::get('/contributions', ContributionsIndex::class)->name('contributions.index');
    Route::get('/contributions/create', ContributionsForm::class)->name('contributions.create');
    Route::get('/contributions/{contribution}/edit', ContributionsForm::class)->name('contributions.edit');

    Route::get('/pledges', PledgesIndex::class)->name('pledges.index');
    Route::get('/pledges/create', PledgesForm::class)->name('pledges.create');
    Route::get('/pledges/{pledge}/edit', PledgesForm::class)->name('pledges.edit');

    // Online Giving & Paystack System
    Route::get('/giving/pay', GivingPay::class)->name('giving.pay');
    Route::get('/giving/my-contributions', GivingMy::class)->name('giving.my');
    Route::post('/giving/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/giving/callback', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/giving/receipt/{reference}', [PaymentController::class, 'receipt'])->name('payment.receipt');
    Route::get('/giving/mock-checkout', [PaymentController::class, 'mockCheckout'])->name('payment.mock.checkout');

    // Push Notifications
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/test', [PushNotificationController::class, 'test'])->name('push.test');

    Route::resource('events', EventController::class);
    Route::resource('teams', TeamController::class);

    Route::get('/assignments', AdminAssignmentsIndex::class)->name('assignments.index');
    Route::resource('assignments', AssignmentController::class)->except(['index', 'create', 'edit']);
    Route::resource('household-members', HouseholdMemberController::class);
    Route::resource('message-recipients', MessageRecipientController::class);
    Route::resource('team-positions', TeamPositionController::class);
    Route::resource('team-members', TeamMemberController::class);
});

// Paystack Webhook (excluded from CSRF)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

require __DIR__.'/auth.php';
