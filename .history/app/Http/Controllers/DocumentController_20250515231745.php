<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Affiche la liste des documents de l'utilisateur connecté
     */
    public function index()
    {
        $documents = Auth::user()->documents()
                        ->orderBy('created_at', 'desc')
                        ->get(['id', 'type', 'title', 'file_path', 'file_size', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Télécharge un document spécifique
     */
    public function download($id)
    {
        $document = Auth::user()->documents()->findOrFail($id);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier non trouvé'
            ], 404);
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->title.'.pdf'
        );
    }
}