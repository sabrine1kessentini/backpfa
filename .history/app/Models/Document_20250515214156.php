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
        // Récupère les documents de l'utilisateur connecté via la relation définie dans le modèle User
        $documents = Auth::user()->documents()->latest()->get();
        
        return response()->json([
            'documents' => $documents
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:releve_notes,attestation,certificat',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Auth::user()->documents()->create([
            'type' => $request->type,
            'title' => $request->title,
            'file_path' => $path,
            'file_size' => $file->getSize()
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document
        ], 201);
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);

        // Vérifie que le document appartient à l'utilisateur connecté
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Vérifie que le fichier existe
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        // Télécharge le fichier
        return Storage::disk('public')->download($document->file_path, $document->title . '.pdf');
    }
}