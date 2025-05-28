<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        try {
            // Méthode 1: Utilisation directe de JWTAuth
            $user = JWTAuth::user();
            
            Log::info('User ID: ' . ($user ? $user->id : 'null'));
            
            if (!$user) {
                Log::error('Utilisateur non authentifié');
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $payments = $user->payments()
                        ->orderBy('created_at', 'desc')
                        ->get(['id', 'payment_mode', 'amount', 'reference', 'status', 'created_at']);

            Log::info('Nombre de paiements trouvés: ' . $payments->count());
            Log::info('Paiements: ' . $payments->toJson());

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans PaymentController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}