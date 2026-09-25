<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdMemberController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageRecipientController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\PledgeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TeamPositionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('people', PersonController::class);
Route::resource('households', HouseholdController::class);
Route::resource('household-members', HouseholdMemberController::class);
Route::resource('groups', GroupController::class);
Route::resource('group-members', GroupMemberController::class);
Route::resource('services', ServiceController::class);
Route::resource('events', EventController::class);
Route::resource('attendances', AttendanceController::class);
Route::resource('funds', FundController::class);
Route::resource('contributions', ContributionController::class);
Route::resource('pledges', PledgeController::class);
Route::resource('messages', MessageController::class);
Route::resource('message-recipients', MessageRecipientController::class);
Route::resource('teams', TeamController::class);
Route::resource('team-positions', TeamPositionController::class);
Route::resource('team-members', TeamMemberController::class);
Route::resource('assignments', AssignmentController::class);
