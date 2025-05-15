<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        // Utilisation explicite de la relation
        $documents = $user->documents()->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function download($id): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $document = $user->documents()->findOrFail($id);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        
        if (!$storage->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        return $storage->download(
            $document->file_path,
            $document->title.'.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$document->title.'.pdf"'
            ]
        );
    }
}