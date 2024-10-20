<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;


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

// Route to get the authenticated user
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Test route
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:staff')->post('/staff_logout', [AuthController::class, 'logoutStaff']);

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify_email', [AuthController::class, 'verifyEmail']);
Route::post('/reset_password', [AuthController::class, 'resetPassword']);
Route::post('/addgroup', [AuthController::class, 'addgroup']);
Route::get('/get_staff', [StaffController::class, 'getStaff']);


Route::get('/test', [AuthController::class, 'test']);
// Logout route
