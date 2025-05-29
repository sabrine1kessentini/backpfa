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

   public function store(Request $request)
{
    $request->validate([
        'matiere' => 'required|string',
        'note' => 'required|numeric|min:0|max:20',
        'semestre' => 'required|in:1,2',
        'commentaire' => 'nullable|string'
    ]);

    try {
        $user = JWTAuth::parseToken()->authenticate();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $note = $user->notes()->create([
            'matiere' => $request->matiere,
            'note' => $request->note,
            'semestre' => $request->semestre,
            'commentaire' => $request->commentaire
        ]);

        // Créer la notification
        $notification = $user->notifications()->create([
            'message' => 'Nouvelle note publiée en ' . $request->matiere . ' !',
            'type' => 'note'
        ]);

        // Cibler tous les utilisateurs (ou ajustez selon vos besoins)
        $notification->targets()->create([
            'target_type' => 'all',
            'target_value' => null
        ]);

        // Optionnel: Diffuser via WebSocket
        // event(new NewNotification($notification));

        return response()->json([
            'note' => $note,
            'notification' => $notification
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors de la création de la note',
            'details' => $e->getMessage()
        ], 500);
    }
}
}
