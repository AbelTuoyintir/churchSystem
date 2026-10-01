<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\HouseholdMemberController;
use App\Http\Controllers\MessageRecipientController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamPositionController;
use App\Livewire\Groups\Form as GroupsForm;
use App\Livewire\Groups\Index as GroupsIndex;
use App\Livewire\Households\Form as HouseholdsForm;
use App\Livewire\Households\Index as HouseholdsIndex;
use App\Livewire\Messages\Form as MessagesForm;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Messages\Show as MessagesShow;
use App\Livewire\People\Form as PeopleForm;
use App\Livewire\People\Index as PeopleIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('people.index');
    })->name('dashboard');

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
});

Route::resource('household-members', HouseholdMemberController::class);
Route::resource('services', ServiceController::class);
Route::resource('events', EventController::class);
Route::resource('attendances', AttendanceController::class);
Route::resource('funds', FundController::class);
Route::resource('contributions', ContributionController::class);
Route::resource('pledges', PledgeController::class);
Route::resource('message-recipients', MessageRecipientController::class);
Route::resource('teams', TeamController::class);
Route::resource('team-positions', TeamPositionController::class);
Route::resource('team-members', TeamMemberController::class);
Route::resource('assignments', AssignmentController::class);

require __DIR__.'/auth.php';
