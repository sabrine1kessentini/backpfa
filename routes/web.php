<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-notification/{id}', function($id) {
    $emploi = \App\Models\Emploi::find($id);
    if ($emploi) {
        $result = \App\Models\Emploi::createNotification($emploi);
        return response()->json(['success' => $result]);
    }
    return response()->json(['error' => 'Emploi non trouvé'], 404);
});

