<?php

namespace App\Http\Middleware;

use App\Helpers\JwtHelper;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware для проверки JWT токена в API запросах
 */
class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided',
            ], 401);
        }

        try {
            $secret = config('app.key');
            $payload = JwtHelper::decodeToken($token, $secret);

            // Проверяем срок действия токена
            if (isset($payload->exp) && $payload->exp < time()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token expired',
                ], 401);
            }

            // Добавляем данные пользователя в request для использования в контроллерах
            $request->attributes->add([
                'jwt_user' => $payload,
                'user_id' => $payload->user_id ?? null,
                'user_email' => $payload->email ?? null,
                'user_role' => $payload->role ?? null,
                'user_guard' => $payload->guard ?? null,
            ]);

            return $next($request);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired',
            ], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token signature',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}

