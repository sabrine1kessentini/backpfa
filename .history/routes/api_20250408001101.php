<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmploiController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/mon-emploi', [EmploiController::class, 'getEmploi']);
    
    // Route protégée pour admin
    Route::post('/admin/emplois/{groupe}', [EmploiController::class, 'uploadEmploi'])
         ->middleware('can:admin');
});