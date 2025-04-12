<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emploi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Events\EmploiUpdated;

class EmploiController extends Controller
{
    public function getEmploi(Request $request)
    {
        $user = $request->user();
        
        if (!$user->groupe) {
            return response()->json([
                'error' => 'Aucun groupe attribué',
                'default_image' => asset('images/emploi/default.jpg')
            ], 400);
        }

        $emploi = Emploi::where('groupe', $user->groupe)->first();

        return response()->json([
            'groupe' => $user->groupe,
            'image_url' => $emploi ? asset('storage/emploi/'.$emploi->image_path) : asset('images/emploi/default.jpg')
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

        // Supprimer l'ancienne image
        $existing = Emploi::where('groupe', $request->groupe)->first();
        if ($existing && $existing->image_path) {
            Storage::delete('public/emploi/'.$existing->image_path);
        }

        // Enregistrer la nouvelle image
        $imageName = time().'_'.$request->groupe.'.'.$request->image->extension();
        $request->image->storeAs('public/emploi', $imageName);

        // Mettre à jour la BDD
        $emploi = Emploi::updateOrCreate(
            ['groupe' => $request->groupe],
            ['image_path' => $imageName]
        );

        // Envoyer la notification
        event(new EmploiUpdated($request->groupe));

        return response()->json([
            'message' => 'Emploi du temps mis à jour avec succès',
            'image_url' => asset('storage/emploi/'.$imageName)
        ]);
    }
}