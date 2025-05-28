<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth; // Ajoutez cette ligne

class PaymentController extends Controller
{
    public function index()
    {
        // Méthode 1: Utilisation directe de JWTAuth
        $user = JWTAuth::user();
        
        // Méthode 2: Alternative avec l'injection de dépendance
        // $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $payments = $user->payments()
                    ->orderBy('created_at', 'desc')
                    ->get(['id', 'payment_mode', 'amount', 'reference', 'status', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }
}