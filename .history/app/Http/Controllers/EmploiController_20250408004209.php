<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emploi;

class EmploiController extends Controller
{
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
}