<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    // Lister les documents de l'utilisateur
    public function index()
    {
        return response()->json([
            'documents' => Auth::user()->documents,
            'types' => Document::allowedTypes()
        ]);
    }

    // Téléverser un nouveau document
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Document::allowedTypes())),
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:2048'
        ]);

        $user = Auth::user();
        $file = $request->file('file');
        
        // Générer un nom de fichier unique
        $fileName = Str::slug($user->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'documents/' . $user->id . '/' . $fileName;

        // Stocker le fichier
        Storage::put($filePath, file_get_contents($file));

        // Enregistrer en base
        $document = Document::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'title' => $request->title,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'is_verified' => false
        ]);

        return response()->json($document, 201);
    }

    // Télécharger un document
    public function download($id)
    {
        $document = Auth::user()->documents()->findOrFail($id);

        if (!Storage::exists($document->file_path)) {
            abort(404);
        }

        return Storage::download($document->file_path, $document->title . '.pdf');
    }

    // Supprimer un document
    public function destroy($id)
    {
        $document = Auth::user()->documents()->findOrFail($id);

        Storage::delete($document->file_path);
        $document->delete();

        return response()->json(null, 204);
    }
}