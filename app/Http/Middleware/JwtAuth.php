<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JwtAuth
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                Log::error('Token manquant dans la requête');
                return response()->json(['error' => 'Token manquant'], 401);
            }

            $decoded = JWT::decode($token, new Key(config('jwt.secret'), 'HS256'));
            
            // Ajouter l'utilisateur à la requête
            $request->merge(['user' => $decoded]);
            
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Erreur d\'authentification JWT: ' . $e->getMessage());
            return response()->json(['error' => 'Token invalide'], 401);
        }
    }
} 