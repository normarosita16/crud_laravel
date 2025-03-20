<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController; // Import RoleController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public accessible API
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/roles', [RoleController::class, 'store']); // Create a new role
Route::get('/roles', [RoleController::class, 'index']);  // Get list of roles

// Authenticated only API
Route::middleware('auth:api')->group(function() {
    Route::get('/me', [UserController::class, 'me']);

    // Role Management API
    
});

Route::post('/logout', [AuthController::class, 'logout']);
