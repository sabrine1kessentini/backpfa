<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User; // Import explicite
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        return response()->json([
            'documents' => $user->documents()->get(),
            'types' => Document::allowedTypes()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Document::allowedTypes())),
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:2048'
        ]);

        /** @var User $user */
        $user = Auth::user();
        $file = $request->file('file');
        
        $fileName = Str::slug($user->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'documents/' . $user->id . '/' . $fileName;

        Storage::put($filePath, file_get_contents($file));

        $document = $user->documents()->create([
            'type' => $request->type,
            'title' => $request->title,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'is_verified' => false
        ]);

        return response()->json($document, 201);
    }

    public function download($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $document = $user->documents()->findOrFail($id);

        if (!Storage::exists($document->file_path)) {
            abort(404);
        }

        return Storage::download($document->file_path, $document->title . '.pdf');
    }

    public function destroy($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $document = $user->documents()->findOrFail($id);

        Storage::delete($document->file_path);
        $document->delete();

        return response()->json(null, 204);
    }
}