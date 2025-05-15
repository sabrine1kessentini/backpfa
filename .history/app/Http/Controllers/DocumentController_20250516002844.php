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
        $documents = Auth::user()->documents()->latest()->get();
        
        return response()->json(['documents' => $documents]);
    }

    public function downloadDocument($id)
    {
        $document = Document::where('user_id', Auth::id())
                          ->findOrFail($id);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->title.'.pdf'
        );
    }
}