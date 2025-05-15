<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Utilisez la relation avec l'annotation de type
        $documents = $user->documents()->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function download($id)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $document = $user->documents()->findOrFail($id);

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