<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emploi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmploiController extends Controller
{
    public function getEmploi(Request $request)
    {
        $user = $request->user();
        
        if (!$user->groupe) {
            return response()->json([
                'error' => 'Aucun groupe attribué',
                'default_image' => asset('images/emploi/emploi.jpeg')
            ], 400);
        }

        $emploi = Emploi::where('groupe', $user->groupe)->first();

        return response()->json([
            'groupe' => $user->groupe,
            'image_url' => $emploi ? asset('images/emploi/'.$emploi->image_path) : asset('images/emploi/emploi.jpeg')
        ]);
    }

    public function updateEmploi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groupe' => 'required|string|max:10',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Supprimer l'ancienne image si elle existe
        $existing = Emploi::where('groupe', $request->groupe)->first();
        if ($existing && $existing->image_path) {
            Storage::delete('public/'.$existing->image_path);
        }

        // Stocker la nouvelle image
        $path = $request->file('image')->store('public/emploi');
        $publicPath = str_replace('public/', '', $path);

        // Mettre à jour ou créer l'entrée
        Emploi::updateOrCreate(
            ['groupe' => $request->groupe],
            ['image_path' => $publicPath]
        );

        return response()->json([
            'message' => 'Emploi du temps mis à jour avec succès',
            'image_url' => asset('storage/'.$publicPath),
            'notification' => [
            'title' => 'Mise à jour emploi du temps',
            'message' => 'L\'emploi du temps de votre groupe a été mis à jour',
            'groupe' => $request->groupe,
            'time' => now()->format('Y-m-d H:i:s')
        ]
        ]);
    }
}