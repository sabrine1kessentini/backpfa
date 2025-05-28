<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NoteController extends Controller
{
public function index(Request $request)
    {
        $request->validate([
            'semestre' => 'sometimes|in:1,2'
        ]);

        try {
            // Méthode 1 : Avec JWT
            $user = JWTAuth::parseToken()->authenticate();
            
            // Méthode alternative : Avec guard API
            // $user = auth()->guard('api')->user();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $notes = $user->notes()
                ->when($request->semestre, function($query, $semestre) {
                    return $query->where('semestre', $semestre);
                })
                ->orderBy('matiere')
                ->get(['matiere', 'note', 'semestre', 'created_at']);

            return response()->json($notes);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur d\'authentification',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
