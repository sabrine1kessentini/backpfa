<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
{
    \Log::debug('Tentative de connexion', [
        'email' => $request->email,
        'provided_password' => $request->password,
        'ip' => $request->ip()
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        \Log::error('Utilisateur non trouvé');
        return response()->json(['error' => 'Identifiants invalides'], 401);
    }

    \Log::debug('Utilisateur trouvé', [
        'id' => $user->id,
        'password_hash' => $user->password
    ]);

    \Log::debug('Vérification mot de passe', [
        'check_result' => \Hash::check($request->password, $user->password)
    ]);

    if (!$token = auth()->attempt($request->only('email', 'password'))) {
        \Log::error('Échec auth attempt', [
            'attempt_result' => auth()->attempt($request->only('email', 'password'))
        ]);
        return response()->json(['error' => 'Identifiants invalides'], 401);
    }

    return $this->respondWithToken($token);
}
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function userProfile()
    {
        return response()->json(JWTAuth::user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}