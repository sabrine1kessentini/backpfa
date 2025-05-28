<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmploiController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes sont préfixées par '/api' et protégées par le middleware JWT
|
*/

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('jwt.auth')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'getUserProfile']);
    
    // Emploi du temps
    Route::get('/mon-emploi', [EmploiController::class, 'getEmploi']);
    
    // Routes pour les documents
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download']);

    // Paiements

    Route::get('/payments', [PaymentController::class, 'index']);

    
    Route::get('/notes', [NoteController::class, 'index']);

});
    
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });