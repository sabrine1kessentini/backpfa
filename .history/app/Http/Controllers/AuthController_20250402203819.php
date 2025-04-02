<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authentifie un utilisateur et retourne un token JWT
     */
    public function login(Request $request)
    {
        // Log des tentatives de connexion
        Log::debug('Tentative de connexion', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        // Récupération de l'utilisateur
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::error('Utilisateur non trouvé', ['email' => $request->email]);
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        // Vérification du mot de passe
        if (!Hash::check($request->password, $user->password)) {
            Log::error('Mot de passe incorrect', ['user_id' => $user->id]);
            return response()->json(['error' => 'Identifiants invalides'], 401);
        }

        // Génération du token JWT
        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            Log::error('Échec génération token', ['user_id' => $user->id]);
            return response()->json(['error' => 'Impossible de créer le token'], 500);
        }

        Log::info('Connexion réussie', ['user_id' => $user->id]);
        return $this->respondWithToken($token);
    }

    /**
     * Déconnecte l'utilisateur (invalide le token)
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            Log::info('Déconnexion réussie', ['user_id' => JWTAuth::user()->id]);
            return response()->json(['message' => 'Déconnexion réussie']);
        } catch (\Exception $e) {
            Log::error('Échec déconnexion', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Échec de la déconnexion'], 500);
        }
    }

    /**
     * Retourne le profil de l'utilisateur authentifié
     */
    public function userProfile()
    {
        try {
            $user = JWTAuth::user();
            Log::debug('Accès profil', ['user_id' => $user->id]);
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Erreur profil', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Utilisateur non trouvé'], 404);
        }
    }

    /**
     * Formatte la réponse avec le token
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => JWTAuth::user()
        ]);
    }
}