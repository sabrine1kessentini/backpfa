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

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        
        if (!$disk->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        // Solution alternative si l'erreur persiste
        $path = storage_path('app/public/' . $document->file_path);
        $name = $document->title . '.pdf';

        return response()->download($path, $name, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $name . '"'
        ]);
    }
}