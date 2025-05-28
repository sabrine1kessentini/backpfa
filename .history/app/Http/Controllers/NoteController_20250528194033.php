<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Note;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NoteController extends Controller
{
public function index(Request $request)
{
    $request->validate(['semestre' => 'sometimes|in:1,2']);

    $user = auth()->guard('api')->user(); // Récupère l'utilisateur connecté

    if (!$user) {
        return response()->json(['error' => 'Non authentifié'], 401);
    }

    $notes = $user->notes()
        ->when($request->semestre, fn($q, $semestre) => $q->where('semestre', $semestre))
        ->orderBy('matiere')
        ->get(['matiere', 'note', 'semestre', 'created_at']);

    return response()->json($notes); // Retourne les notes au format JSON
}
}
