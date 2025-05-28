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
        /** @var User $user */
        $user = Auth::user();
        $document = $user->documents()->findOrFail($id);

        try {
            // Utiliser le chemin stocké dans la base de données
            $filePath = storage_path('app/' . $document->file_path);

            // Vérifier si le fichier existe
            if (!file_exists($filePath)) {
                throw new \Exception("Le fichier n'existe pas: " . $filePath);
            }

            // Vérifier si le fichier est lisible
            if (!is_readable($filePath)) {
                throw new \Exception("Le fichier n'est pas accessible: " . $filePath);
            }

            // Créer une notification pour le téléchargement
            $notification = Notification::create([
                'message' => "Vous avez téléchargé le document: {$document->title}",
                'user_id' => $user->id
            ]);

            // Ajouter les cibles de la notification
            $notification->targets()->create([
                'target_type' => 'all',
                'target_value' => null
            ]);

            // Télécharger le fichier
            return response()->download($filePath, $document->title . '.pdf', [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $document->title . '.pdf"'
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur de téléchargement: " . $e->getMessage());
            abort(500, "Une erreur est survenue lors du téléchargement : " . $e->getMessage());
        }
    }
}