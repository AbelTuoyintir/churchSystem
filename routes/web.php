<?php

use App\Http\Controllers\ProfileController;
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
use App\Livewire\Services\Index as ServicesIndex;
use App\Livewire\Services\Form as ServicesForm;
use App\Livewire\Attendance\Index as AttendanceIndex;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/people', PeopleIndex::class)->name('people.index');
    Route::get('/people/create', PeopleForm::class)->name('people.create');
    Route::get('/people/{person}/edit', PeopleForm::class)->name('people.edit');

    Route::get('/households', HouseholdsIndex::class)->name('households.index');
    Route::get('/households/create', HouseholdsForm::class)->name('households.create');
    Route::get('/households/{household}/edit', HouseholdsForm::class)->name('households.edit');

    Route::get('/groups', GroupsIndex::class)->name('groups.index');
    Route::get('/groups/create', GroupsForm::class)->name('groups.create');
    Route::get('/groups/{group}/edit', GroupsForm::class)->name('groups.edit');

    Route::get('/services', ServicesIndex::class)->name('services.index');
    Route::get('/services/create', ServicesForm::class)->name('services.create');
    Route::get('/services/{service}/edit', ServicesForm::class)->name('services.edit');

    Route::get('/attendances', AttendanceIndex::class)->name('attendances.index');
});
Route::resource('household-members', HouseholdMemberController::class);
Route::resource('events', EventController::class);
Route::resource('funds', FundController::class);
Route::resource('contributions', ContributionController::class);
Route::resource('pledges', PledgeController::class);
Route::resource('messages', MessageController::class);
Route::resource('message-recipients', MessageRecipientController::class);
Route::resource('teams', TeamController::class);
Route::resource('team-positions', TeamPositionController::class);
Route::resource('team-members', TeamMemberController::class);
Route::resource('assignments', AssignmentController::class);
