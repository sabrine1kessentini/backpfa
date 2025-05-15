<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
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

    // Renommez cette méthode en 'download' pour correspondre à la route
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
            $document->title.'.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$document->title.'.pdf"'
            ]
        );
    }
}