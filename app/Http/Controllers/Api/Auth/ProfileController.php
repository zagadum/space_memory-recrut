<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Auth",
    description: "API для авторизации пользователей"
)]
class ProfileController extends Controller
{
    #[OA\Get(
        path: "/auth/me",
        summary: "Получить информацию текущего пользователя",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "u_1"),
                        new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Admin User")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Не авторизован"
            )
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        $jwtUser = $request->attributes->get('jwt_user');

        return response()->json([
            'id' => $this->generateUserId($jwtUser->role ?? 'user', $jwtUser->user_id ?? 0),
            'email' => $jwtUser->email ?? '',
            'name' => $this->getUserName($jwtUser),
        ], 200);
    }

    #[OA\Get(
        path: "/auth/profile",
        summary: "Получить профиль текущего пользователя",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                                new OA\Property(property: "role", type: "string", example: "admin"),
                                new OA\Property(property: "guard", type: "string", example: "admin")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Не авторизован",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token not provided")
                    ]
                )
            )
        ]
    )]
    public function show(Request $request): JsonResponse
    {
        // Данные пользователя из JWT токена добавлены middleware VerifyJwtToken
        $jwtUser = $request->attributes->get('jwt_user');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $jwtUser->user_id ?? null,
                'email' => $jwtUser->email ?? null,
                'role' => $jwtUser->role ?? null,
                'guard' => $jwtUser->guard ?? null,
                'issued_at' => isset($jwtUser->iat) ? date('Y-m-d H:i:s', $jwtUser->iat) : null,
                'expires_at' => isset($jwtUser->exp) ? date('Y-m-d H:i:s', $jwtUser->exp) : null,
            ],
        ], 200);
    }

    #[OA\Post(
        path: "/auth/logout",
        summary: "Выход из системы (информационный endpoint)",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный выход",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Logged out successfully")
                    ]
                )
            )
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        // Примечание: JWT токены stateless, поэтому реальный logout происходит на клиенте
        // путем удаления токена из хранилища. Этот endpoint нужен для логирования
        // или других действий при выходе.

        $jwtUser = $request->attributes->get('jwt_user');

        // Здесь можно добавить логирование выхода пользователя
        Log::info('User logged out', [
            'user_id' => $jwtUser->user_id ?? null,
            'role' => $jwtUser->role ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully. Please remove the token from client storage.',
        ], 200);
    }

    #[OA\Post(
        path: "/auth/verify-token",
        summary: "Проверить валидность токена",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Токен валиден",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "valid", type: "boolean", example: true),
                        new OA\Property(property: "expires_in", type: "integer", example: 2592000, description: "Seconds until expiration")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Токен невалиден",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid token")
                    ]
                )
            )
        ]
    )]
    public function verifyToken(Request $request): JsonResponse
    {
        $jwtUser = $request->attributes->get('jwt_user');

        $expiresIn = 0;
        if (isset($jwtUser->exp)) {
            $expiresIn = $jwtUser->exp - time();
        }

        return response()->json([
            'success' => true,
            'valid' => true,
            'expires_in' => max(0, $expiresIn),
            'expires_at' => isset($jwtUser->exp) ? date('Y-m-d H:i:s', $jwtUser->exp) : null,
        ], 200);
    }

    /**
     * Генерирует ID пользователя для API
     */
    private function generateUserId(string $role, int $id): string
    {
        $prefix = match ($role) {
            'admin' => 'a',
            'student' => 's',
            'teacher' => 't',
            'manager' => 'm',
            'franchisee' => 'f',
            default => 'u',
        };

        return $prefix . '_' . $id;
    }

    /**
     * Получает имя пользователя из JWT payload
     */
    private function getUserName($jwtUser): string
    {
        // Попробуем получить имя из разных источников
        if (isset($jwtUser->name)) {
            return $jwtUser->name;
        }

        return $jwtUser->email ?? 'Unknown User';
    }
}

