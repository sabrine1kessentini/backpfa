<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

public function download($id): BinaryFileResponse
{
    /** @var User $user */
    $user = Auth::user();
    $document = $user->documents()->findOrFail($id);

    $path = storage_path('app/public/' . $document->file_path);
    $name = $document->title . '.pdf';

    return response()->download($path, $name, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $name . '"'
    ]);
}
}