<?php
// app/Http/Controllers/EmploiController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emploi;
use Illuminate\Support\Facades\Storage;

class EmploiController extends Controller
{
    /**
     * Récupère l'emploi du temps de l'utilisateur connecté
     */
    public function getEmploi(Request $request)
    {
        $user = $request->user();
        
        if (!$user->groupe) {
            return response()->json(['error' => 'Aucun groupe attribué'], 400);
        }

        $emploi = Emploi::where('groupe', $user->groupe)->first();

        if (!$emploi) {
            return response()->json(['error' => 'Aucun emploi trouvé pour ce groupe'], 404);
        }

        return response()->json([
            'groupe' => $user->groupe,
            'image_url' => asset('storage/' . $emploi->image_path)
        ]);
    }

    /**
     * Upload un nouvel emploi du temps pour un groupe
     */
    public function uploadEmploi(Request $request, $groupe)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = $request->file('image')->store('public/emplois');

        Emploi::updateOrCreate(
            ['groupe' => $groupe],
            ['image_path' => str_replace('public/', '', $path)]
        );

        return response()->json(['success' => 'Emploi du temps mis à jour']);
    }
}