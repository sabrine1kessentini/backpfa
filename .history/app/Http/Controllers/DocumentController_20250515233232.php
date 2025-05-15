<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
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
        'data' => $documents // Bien structuré dans une clé 'data'
    ]);
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