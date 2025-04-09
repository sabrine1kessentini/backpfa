namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        return $next($request);
    }
}