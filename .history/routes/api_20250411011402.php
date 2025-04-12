<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmploiController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
    Route::get('/mon-emploi', [EmploiController::class, 'getEmploi']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents/generate/{type}', [DocumentController::class, 'generateDocument']);
    Route::get('/documents/download/{id}', [DocumentController::class, 'download']);
});