<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuideController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ReminderController;
use App\Http\Controllers\API\SupplementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/guides', [GuideController::class, 'index']);

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // User Info
    Route::get('/user', fn(Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // Supplements API
    Route::get('/supplements', [SupplementController::class, 'index']);
    Route::post('/supplements/add', [SupplementController::class, 'store']);
    Route::get('/supplements/{id}', [SupplementController::class, 'show']);
    Route::put('/supplements/{id}', [SupplementController::class, 'update']);
    Route::delete('/supplements/{id}', [SupplementController::class, 'destroy']);

    // Calendar API (Retrieve supplements for specific dates)
    Route::get('/calendar/{date}', [SupplementController::class, 'getByDate']);

    // Profile API
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);

    // Reminder API
    Route::get('/reminders', [ReminderController::class, 'index']);
    Route::post('/reminders/add', [ReminderController::class, 'store']);
    Route::delete('/reminders/{id}', [ReminderController::class, 'destroy']);
});