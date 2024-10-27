<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProgramController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify_email', [AuthController::class, 'verifyEmail']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);

// Group routes
Route::post('/add_group', [GroupController::class, 'addGroup']);
Route::get('/get_group', [GroupController::class, 'getGroup']);

//Staff routes
Route::get('/get_staff', [StaffController::class, 'getStaff']);
//
Route::post('/add_program', [ProgramController::class, 'addProgram']);
Route::get('/get_program', [ProgramController::class, 'getProgram']);
Route::get('/get-all-programs', [ProgramController::class, 'getAllPrograms']);
Route::post('/update-program', [ProgramController::class, 'updateProgram']);


Route::post('/update_user_group', [GroupController::class, 'updateUserGroup']);
Route::post('/update-group-name', [GroupController::class, 'updateGroupName']);
Route::delete('/delete-group-name', [GroupController::class, 'deleteGroupName']);


// Routes protected by 'auth:api' middleware
Route::middleware('auth:api')->group(function () {
    // Group routes
    // Route::post('/update_user_group', [GroupController::class, 'updateUserGroup']);
    // Route::post('/update-group-name', [GroupController::class, 'updateGroupName']);
    // Route::delete('/delete-group-name', [GroupController::class, 'deleteGroupName']);

    // End Group routes
    //
    //Program routse
//

    //
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    // End Authentication routes
});

// Test route (not protected)
Route::get('/test', [AuthController::class, 'test']);
