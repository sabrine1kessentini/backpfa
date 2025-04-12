<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = auth()->user()->documents()->get();
        return response()->json($documents);
    }

    public function generateDocument(Request $request, $type)
    {
        $user = auth()->user();
        
        // Valider le type de document
        if (!in_array($type, ['releve_notes', 'attestation_scolarite', 'certificat'])) {
            return response()->json(['error' => 'Type de document invalide'], 400);
        }

        // Générer le PDF sécurisé
        $pdf = PDF::loadView('documents.'.$type, ['user' => $user])
                  ->setPaper('a4')
                  ->setOption('isPhpEnabled', true);

        // Ajouter une protection
        $pdf->setEncryption(
            config('app.doc_password'), 
            $user->id, 
            ['print', 'modify', 'copy', 'annot-forms']
        );

        // Sauvegarder le fichier
        $fileName = "{$type}_{$user->id}_".now()->format('YmdHis').'.pdf';
        $filePath = "documents/{$user->id}/{$fileName}";
        
        Storage::put($filePath, $pdf->output());

        // Enregistrer dans la base
        $document = Document::create([
            'user_id' => $user->id,
            'document_type' => $type,
            'file_path' => $filePath,
            'file_name' => $fileName
        ]);

        return response()->json($document);
    }

    public function download($id)
    {
        $document = Document::where('user_id', auth()->id())->findOrFail($id);
        
        // Vérifier si le fichier existe
        if (!Storage::exists($document->file_path)) {
            abort(404);
        }

        // Incrémenter le compteur
        $document->increment('download_count');

        return Storage::download($document->file_path, $document->file_name, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}