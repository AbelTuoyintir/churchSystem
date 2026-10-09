<?php

use App\Http\Controllers\HouseholdMemberController;
use App\Http\Controllers\MessageRecipientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamPositionController;
use App\Livewire\Assignments\Form as AssignmentsForm;
use App\Livewire\Assignments\Index as AssignmentsIndex;
use App\Livewire\Attendance\Index as AttendanceIndex;
use App\Livewire\Contributions\Form as ContributionsForm;
use App\Livewire\Contributions\Index as ContributionsIndex;
use App\Livewire\Events\Form as EventsForm;
use App\Livewire\Events\Index as EventsIndex;
use App\Livewire\Funds\Form as FundsForm;
use App\Livewire\Funds\Index as FundsIndex;
use App\Livewire\Groups\Form as GroupsForm;
use App\Livewire\Groups\Index as GroupsIndex;
use App\Livewire\Households\Form as HouseholdsForm;
use App\Livewire\Households\Index as HouseholdsIndex;
use App\Livewire\Members\Index as MembersIndex;
use App\Livewire\Messages\Form as MessagesForm;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Messages\Show as MessagesShow;
use App\Livewire\People\Form as PeopleForm;
use App\Livewire\People\Index as PeopleIndex;
use App\Livewire\Personas\Index as PersonasIndex;
use App\Livewire\Pledges\Form as PledgesForm;
use App\Livewire\Pledges\Index as PledgesIndex;
use App\Livewire\Services\Form as ServicesForm;
use App\Livewire\Services\Index as ServicesIndex;
use App\Livewire\Teams\Form as TeamsForm;
use App\Livewire\Teams\Index as TeamsIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'member') {
            return redirect()->route('members.portal');
        }
        return redirect()->route('people.index');
    })->name('dashboard');

    Route::get('/member-portal', MembersIndex::class)->name('members.portal');
    Route::get('/personas', PersonasIndex::class)->name('personas.index');

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

    Route::get('/teams', TeamsIndex::class)->name('teams.index');
    Route::get('/teams/create', TeamsForm::class)->name('teams.create');
    Route::get('/teams/{team}/edit', TeamsForm::class)->name('teams.edit');

    Route::get('/events', EventsIndex::class)->name('events.index');
    Route::get('/events/create', EventsForm::class)->name('events.create');
    Route::get('/events/{event}/edit', EventsForm::class)->name('events.edit');

    Route::get('/assignments', AssignmentsIndex::class)->name('assignments.index');
    Route::get('/assignments/create', AssignmentsForm::class)->name('assignments.create');
    Route::get('/assignments/{assignment}/edit', AssignmentsForm::class)->name('assignments.edit');

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
});

Route::resource('household-members', HouseholdMemberController::class);
Route::resource('message-recipients', MessageRecipientController::class);
Route::resource('team-positions', TeamPositionController::class);
Route::resource('team-members', TeamMemberController::class);

require __DIR__.'/auth.php';
