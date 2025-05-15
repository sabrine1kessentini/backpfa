<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $documents = $user->documents()->latest()->get();
        
        return response()->json(['documents' => $documents]);
    }

    public function downloadDocument($id)
    {
        $document = Document::where('user_id', Auth::id())
                          ->findOrFail($id);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        
        if (!$storage->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        return $storage->download(
            $document->file_path,
            $document->title.'.pdf'
        );
    }
}