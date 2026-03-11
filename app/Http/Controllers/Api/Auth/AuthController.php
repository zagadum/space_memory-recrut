<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\JwtHelper;
use App\Http\Controllers\Controller;
use App\Models\RecrutingStudent;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Auth",
    description: "API для авторизации пользователей"
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/auth/sign-in",
        summary: "Авторизация пользователя",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(
                        property: "email",
                        type: "string",
                        format: "email",
                        example: "admin@example.com"
                    ),
                    new OA\Property(
                        property: "password",
                        type: "string",
                        format: "password",
                        example: "secret123"
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешная авторизация",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "mock.jwt.token"),
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(property: "id", type: "string", example: "u_1"),
                                new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                                new OA\Property(property: "name", type: "string", example: "Admin"),
                                new OA\Property(property: "role", type: "string", example: "admin"),
                                new OA\Property(property: "initials", type: "string", example: "AD")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Неверные учетные данные",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid credentials")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Validation error"),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            example: ["email" => ["The email field is required."]]
                        )
                    ]
                )
            )
        ]
    )]
    public function signIn(Request $request): JsonResponse
    {
        // Валидация входных данных
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $validated['email'];
        $password = $validated['password'];

        // Пытаемся найти пользователя в разных таблицах и проверить пароль
        $user = null;
        $role = null;
        $guard = null;

        // Проверяем студента
        $student = RecrutingStudent::where('email', $email)
            ->where('enabled', 1)
            ->where('deleted', 0)
            ->where('blocked', 0)
            ->first();

        if ($student && Hash::check($password, $student->password)) {
            $user = $student;
            $role = 'student';
            $guard = 'student';
        }

        // Проверяем админа
        if (!$user) {
            $admin = AdminUser::where('email', $email)
                ->where('activated', 1)
                ->where('forbidden', 0)
                ->whereNull('deleted_at')
                ->first();

            if ($admin && Hash::check($password, $admin->password)) {
                $user = $admin;
                $role = 'admin';
                $guard = 'admin';
            }
        }

        // Проверяем менеджера
        if (!$user) {
            $manager = Managers::where('email', $email)
                ->where('enabled', 1)
                ->where('deleted', 0)
                ->first();

            if ($manager && Hash::check($password, $manager->password)) {
                $user = $manager;
                $role = 'manager';
                $guard = 'manager';
            }
        }

        // Проверяем учителя
        if (!$user) {
            $teacher = Teacher::where('email', $email)
                ->where('enabled', 1)
                ->whereNull('deleted')
                ->first();

            if ($teacher && Hash::check($password, $teacher->password)) {
                $user = $teacher;
                $role = 'teacher';
                $guard = 'teacher';
            }
        }

        // Проверяем франчайзи
        if (!$user) {
            $franchisee = Franchisee::where('email', $email)
                ->where('enabled', 1)
                ->where('deleted', 0)
                ->first();

            if ($franchisee && Hash::check($password, $franchisee->password)) {
                $user = $franchisee;
                $role = 'franchisee';
                $guard = 'franchisee';
            }
        }

        // Если пользователь не найден или пароль неверный
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Генерируем JWT токен
        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $role,
            'guard' => $guard,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24 * 30), // 30 дней
        ];

        $secret = config('app.key');
        $token = JwtHelper::createToken($payload, $secret);

        // Формируем данные пользователя для ответа
        $userData = [
            'id' => $this->generateUserId($role, $user->id),
            'email' => $user->email,
            'name' => $this->getUserName($user, $role),
            'role' => $role,
            'initials' => $this->getInitials($user, $role),
        ];

        return response()->json([
            'token' => $token,
            'user' => $userData,
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
     * Получает имя пользователя в зависимости от роли
     */
    private function getUserName($user, string $role): string
    {
        if ($role === 'admin') {
            return trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        }

        if ($role === 'student') {
            return trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));
        }

        if ($role === 'teacher' || $role === 'manager' || $role === 'franchisee') {
            return trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));
        }

        return $user->email;
    }

    /**
     * Получает инициалы пользователя
     */
    private function getInitials($user, string $role): string
    {
        if ($role === 'admin') {
            $first = mb_substr($user->first_name ?? '', 0, 1);
            $last = mb_substr($user->last_name ?? '', 0, 1);
            return mb_strtoupper($first . $last);
        }

        if ($role === 'student' || $role === 'teacher' || $role === 'manager' || $role === 'franchisee') {
            $first = mb_substr($user->name ?? '', 0, 1);
            $last = mb_substr($user->surname ?? '', 0, 1);
            return mb_strtoupper($first . $last);
        }

        return mb_strtoupper(mb_substr($user->email, 0, 2));
    }
}

