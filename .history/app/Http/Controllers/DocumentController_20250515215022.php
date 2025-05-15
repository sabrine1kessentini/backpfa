<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Affiche la liste des documents de l'utilisateur
     */
    public function index()
    {
        $user = Auth::user();
        $documents = $user->documents()->latest()->get();
        
        return response()->json([
            'documents' => $documents
        ]);
    }

    /**
     * Enregistre un nouveau document
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:releve_notes,attestation,certificat',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:2048'
        ]);

        $user = Auth::user();
        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = new Document([
            'type' => $request->type,
            'title' => $request->title,
            'file_path' => $path,
            'file_size' => $file->getSize()
        ]);

        $user->documents()->save($document);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document
        ], 201);
    }

    /**
     * Télécharge un document
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);
        $user = Auth::user();

        if ($document->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($document->file_path, $document->title . '.pdf');
    }
}