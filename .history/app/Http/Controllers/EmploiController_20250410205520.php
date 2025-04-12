<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emploi;
use Illuminate\Support\Facades\Storage;
use App\Events\EmploiUpdated;

class EmploiController extends Controller
{
    public function getEmploi(Request $request)
    {
        $user = $request->user();
        
        if (!$user || !$user->groupe) {
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
}