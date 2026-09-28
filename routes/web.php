<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HouseholdMemberController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageRecipientController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamPositionController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\AssignmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    $user = \App\Models\User::first() ?? \App\Models\User::factory()->create(['role' => 'admin']);
    auth()->login($user);
    return redirect('/people');
})->name('login');

use App\Livewire\People\Index as PeopleIndex;
use App\Livewire\People\Form as PeopleForm;
use App\Livewire\Households\Index as HouseholdsIndex;
use App\Livewire\Households\Form as HouseholdsForm;
use App\Livewire\Groups\Index as GroupsIndex;
use App\Livewire\Groups\Form as GroupsForm;
use App\Livewire\Funds\Index as FundsIndex;
use App\Livewire\Funds\Form as FundsForm;
use App\Livewire\Contributions\Index as ContributionsIndex;
use App\Livewire\Contributions\Form as ContributionsForm;
use App\Livewire\Pledges\Index as PledgesIndex;
use App\Livewire\Pledges\Form as PledgesForm;

Route::middleware(['auth'])->group(function () {
    Route::get('/people', PeopleIndex::class)->name('people.index');
    Route::get('/people/create', PeopleForm::class)->name('people.create');
    Route::get('/people/{person}/edit', PeopleForm::class)->name('people.edit');

    Route::get('/households', HouseholdsIndex::class)->name('households.index');
    Route::get('/households/create', HouseholdsForm::class)->name('households.create');
    Route::get('/households/{household}/edit', HouseholdsForm::class)->name('households.edit');

    Route::get('/groups', GroupsIndex::class)->name('groups.index');
    Route::get('/groups/create', GroupsForm::class)->name('groups.create');
    Route::get('/groups/{group}/edit', GroupsForm::class)->name('groups.edit');

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
Route::resource('services', ServiceController::class);
Route::resource('events', EventController::class);
Route::resource('attendances', AttendanceController::class);
Route::resource('messages', MessageController::class);
Route::resource('message-recipients', MessageRecipientController::class);
Route::resource('teams', TeamController::class);
Route::resource('team-positions', TeamPositionController::class);
Route::resource('team-members', TeamMemberController::class);
Route::resource('assignments', AssignmentController::class);
