<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class DocumentController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $documents = $user->documents()
                        ->orderBy('created_at', 'desc')
                        ->get(['id', 'type', 'title', 'file_path', 'file_size', 'created_at']);
        
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function download($id): BinaryFileResponse
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            Log::info("Tentative de téléchargement du document ID: " . $id . " par l'utilisateur: " . $user->id);

            $document = $user->documents()->findOrFail($id);
            Log::info("Document trouvé: " . json_encode($document->toArray()));

            // Utiliser le chemin stocké dans la base de données
            $filePath = storage_path('app/public/' . $document->file_path);
            Log::info("Chemin du fichier: " . $filePath);

            // Vérifier si le fichier existe
            if (!file_exists($filePath)) {
                Log::error("Fichier non trouvé: " . $filePath);
                return response()->json([
                    'error' => 'Le fichier n\'existe pas sur le serveur',
                    'path' => $filePath
                ], 404);
            }

            // Vérifier si le fichier est lisible
            if (!is_readable($filePath)) {
                Log::error("Fichier non lisible: " . $filePath);
                return response()->json([
                    'error' => 'Le fichier n\'est pas accessible',
                    'path' => $filePath
                ], 403);
            }

            // Vérifier la taille du fichier
            $fileSize = filesize($filePath);
            if ($fileSize === 0) {
                Log::error("Fichier vide: " . $filePath);
                return response()->json([
                    'error' => 'Le fichier est vide',
                    'path' => $filePath
                ], 400);
            }

            Log::info("Taille du fichier: " . $fileSize . " bytes");

            // Créer une notification pour le téléchargement
            try {
                $notification = Notification::create([
                    'message' => "Vous avez téléchargé le document: {$document->title}",
                    'user_id' => $user->id
                ]);

                $notification->targets()->create([
                    'target_type' => 'all',
                    'target_value' => null
                ]);
                Log::info("Notification créée avec succès");
            } catch (\Exception $e) {
                Log::warning("Erreur lors de la création de la notification: " . $e->getMessage());
            }

            // Vérifier le type MIME du fichier
            $mimeType = mime_content_type($filePath);
            Log::info("Type MIME du fichier: " . $mimeType);

            if ($mimeType !== 'application/pdf') {
                Log::warning("Type MIME incorrect: " . $mimeType);
            }

            // Télécharger le fichier
            Log::info("Début du téléchargement du fichier");
            return response()->download(
                $filePath,
                $document->title . '.pdf',
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $document->title . '.pdf"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'Content-Length' => $fileSize
                ]
            );

        } catch (\Exception $e) {
            Log::error("Erreur de téléchargement: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([
                'error' => 'Une erreur est survenue lors du téléchargement',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}