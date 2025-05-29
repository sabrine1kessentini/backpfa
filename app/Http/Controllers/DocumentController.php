<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class DocumentController extends Controller
{
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $documents = $user->documents()
                            ->orderBy('created_at', 'desc')
                            ->get(['id', 'type', 'title', 'file_path', 'file_size', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => $documents
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des documents',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                Log::error("Téléchargement échoué: Utilisateur non authentifié");
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $document = $user->documents()->findOrFail($id);
            Log::info("Tentative de téléchargement du document: " . $document->title);
            
            // Utiliser le disque de stockage configuré
            if (!Storage::disk('public')->exists($document->file_path)) {
                Log::error("Fichier non trouvé: " . $document->file_path);
                return response()->json([
                    'error' => 'Le fichier n\'existe pas sur le serveur'
                ], 404);
            }

            // Vérifier si le fichier est lisible
            if (!Storage::disk('public')->getVisibility($document->file_path)) {
                Log::error("Fichier non accessible: " . $document->file_path);
                return response()->json([
                    'error' => 'Le fichier n\'est pas accessible'
                ], 403);
            }

            // Obtenir la taille du fichier
            $fileSize = Storage::disk('public')->size($document->file_path);
            if ($fileSize === 0) {
                Log::error("Fichier vide: " . $document->file_path);
                return response()->json([
                    'error' => 'Le fichier est vide'
                ], 400);
            }

            // Créer une notification pour le téléchargement
            try {
                $notification = $user->notifications()->create([
                    'message' => "Vous avez téléchargé le document: {$document->title}",
                    'type' => 'document_download'
                ]);

                $notification->targets()->create([
                    'target_type' => 'all',
                    'target_value' => null
                ]);
            } catch (\Exception $e) {
                Log::warning("Erreur lors de la création de la notification: " . $e->getMessage());
            }

            Log::info("Téléchargement réussi du document: " . $document->title);

            // Obtenir le contenu du fichier
            $file = Storage::disk('public')->get($document->file_path);
            
            // Retourner la réponse avec le fichier
            return response($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $document->title . '.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'Content-Length' => $fileSize
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur de téléchargement: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'error' => 'Une erreur est survenue lors du téléchargement',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}