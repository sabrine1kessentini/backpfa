<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        return response()->json([
            'documents' => $user->documents()
                ->select(['id', 'type', 'title', 'file_path', 'created_at'])
                ->get()
        ]);
    }

    public function download($id): StreamedResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $document = $user->documents()->findOrFail($id);

        if (!Storage::exists($document->file_path)) {
            abort(404, 'Fichier non trouvé');
        }

        return Storage::download(
            $document->file_path,
            $document->title . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}