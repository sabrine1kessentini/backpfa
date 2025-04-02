<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/api/login', [AuthController::class, 'login']);

// Routes protégées par JWT
Route::middleware('auth:api')->group(function () {
    Route::post('/api/logout', [AuthController::class, 'logout']);
    Route::get('/api/profile', [AuthController::class, 'profile']);
});
