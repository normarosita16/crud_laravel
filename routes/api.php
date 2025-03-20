<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController; // Import RoleController
use App\Http\Controllers\Api\EmployeeController; // Import EmployeeController

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

Route::post('/roles', [RoleController::class, 'store']); // Create a new role
Route::get('/roles', [RoleController::class, 'index']);  // Get list of roles

   // User Management API
   Route::post('/users', [UserController::class, 'createUser']); // Create user
   Route::get('/users', [UserController::class, 'listUser']); // List users
   Route::get('/users/{id}', [UserController::class, 'viewUser']); // View user by ID
   Route::put('/users/{id}', [UserController::class, 'updateUser']); // Update user
   Route::delete('/users/{id}', [UserController::class, 'delete']); // Delete user

   Route::post('/login', [UserController::class, 'login']); // User login

   // Employee Data API
   Route::get('/employees', [EmployeeController::class, 'fetchData']); // Fetch employee data

// Authenticated only API
Route::middleware('auth:api')->group(function() {
    Route::get('/me', [UserController::class, 'me']);
});
