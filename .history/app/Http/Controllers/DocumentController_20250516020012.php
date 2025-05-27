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
        $documents = $user->documents()
                        ->orderBy('created_at', 'desc')
                        ->get(['id', 'type', 'title', 'file_path', 'file_size', 'created_at']);
        
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

        // Utilisation du disk 'documents' configuré
        $disk = Storage::disk('documents');
        
        if (!$disk->exists($document->file_path)) {
            abort(404, 'Le fichier demandé n\'existe pas');
        }

        // Solution optimale avec contrôle d'accès
        return $disk->download(
            $document->file_path,
            $document->title.'.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$document->title.'.pdf"'
            ]
        );
    }
}