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
        if (file_exists(public_path('images/emploi/'.$existing->image_path))) {
            unlink(public_path('images/emploi/'.$existing->image_path));
        }
    }

    // Enregistrer la nouvelle image
    $imageName = 'emploi_'.$request->groupe.'_'.time().'.'.$request->image->extension(); 
    $request->image->move(public_path('images/emploi'), $imageName);

    // Mettre à jour la BDD
    Emploi::updateOrCreate(
        ['groupe' => $request->groupe],
        ['image_path' => $imageName]
    );

    return response()->json([
        'message' => 'Emploi du temps mis à jour avec succès',
        'image_url' => asset('images/emploi/'.$imageName),
        'notification' => [
            'title' => 'Mise à jour emploi du temps',
            'message' => 'L\'emploi du temps de votre groupe a été mis à jour',
            'groupe' => $request->groupe,
            'time' => now()->format('Y-m-d H:i:s')
        ]
    ]);
}
}