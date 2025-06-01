<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmploiController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReclamationController;

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
Route::post('/refresh-token', [AuthController::class, 'refresh']);

// Routes protégées
Route::middleware(['jwt.auth'])->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'getUserProfile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // Emploi du temps
Route::get('/mon-emploi', [EmploiController::class, 'getEmploi']);
    // Documents
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/{id}/download', [DocumentController::class, 'download']);
        Route::delete('/{id}', [DocumentController::class, 'destroy']);
    });

    // Paiements
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::get('/{id}', [PaymentController::class, 'show']);
    });
    
    // Notes
    Route::prefix('notes')->group(function () {
        Route::get('/', [NoteController::class, 'index']);
        Route::post('/', [NoteController::class, 'store']);
        Route::get('/stats', [NoteController::class, 'stats']);
        Route::get('/semestre/{semestre}', [NoteController::class, 'getBySemestre']);
    });
    
    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/', [NotificationController::class, 'store']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    Route::middleware('jwt.auth')->group(function () {
        Route::post('/reclamations', [ReclamationController::class, 'store']);
        Route::get('/reclamations/{reclamation}', [ReclamationController::class, 'show']);
        Route::get('/user/reclamations', [ReclamationController::class, 'userReclamations']);
        Route::patch('/reclamations/{reclamation}/status', [ReclamationController::class, 'updateStatus']);
    }); 
});