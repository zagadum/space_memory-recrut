<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Settings Users",
    description: "API для управления пользователями системы"
)]
class SettingsUsersController extends Controller
{
    #[OA\Get(
        path: "/settings/users",
        summary: "Получить список пользователей системы",
        tags: ["Settings Users"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Список пользователей",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "items",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "string"),
                                    new OA\Property(property: "email", type: "string"),
                                    new OA\Property(property: "name", type: "string"),
                                    new OA\Property(property: "role", type: "string"),
                                    new OA\Property(property: "status", type: "string"),
                                    new OA\Property(property: "lastLogin", type: "string")
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        // TODO: Получить реальных пользователей из БД
        // $users = AdminUser::where('deleted', 0)
        //     ->select('id', 'email', 'first_name', 'last_name', 'activated', 'forbidden', 'last_login_at')
        //     ->get()
        //     ->map(function($user) {
        //         return [
        //             'id' => 'u_' . $user->id,
        //             'email' => $user->email,
        //             'name' => trim($user->first_name . ' ' . $user->last_name),
        //             'role' => 'admin', // TODO: Определить роль
        //             'status' => $user->activated ? 'active' : 'pending',
        //             'lastLogin' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : null,
        //         ];
        //     });

        $items = [
            [
                'id' => 'u_1',
                'email' => 'admin@example.com',
                'name' => 'Администратор',
                'role' => 'admin',
                'status' => 'active',
                'lastLogin' => '2026-03-06 14:30:00',
            ],
            [
                'id' => 'u_2',
                'email' => 'manager@example.com',
                'name' => 'Менеджер Мария',
                'role' => 'manager',
                'status' => 'active',
                'lastLogin' => '2026-03-05 10:15:00',
            ],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Patch(
        path: "/settings/users/{id}",
        summary: "Обновить пользователя",
        tags: ["Settings Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string"),
                    new OA\Property(property: "role", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Пользователь обновлен"
            )
        ]
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'role' => 'sometimes|string|in:admin,manager,teacher',
            'password' => 'sometimes|string|min:7',
        ]);

        // TODO: Обновить пользователя в БД
        // $userId = str_replace('u_', '', $id);
        // $user = AdminUser::findOrFail($userId);
        //
        // if (isset($validated['name'])) {
        //     $nameParts = explode(' ', $validated['name'], 2);
        //     $user->first_name = $nameParts[0] ?? '';
        //     $user->last_name = $nameParts[1] ?? '';
        // }
        //
        // if (isset($validated['email'])) {
        //     $user->email = $validated['email'];
        // }
        //
        // if (isset($validated['password'])) {
        //     $user->password = Hash::make($validated['password']);
        // }
        //
        // $user->save();

        $user = [
            'id' => $id,
            'email' => $validated['email'] ?? 'updated@example.com',
            'name' => $validated['name'] ?? 'Updated User',
            'role' => $validated['role'] ?? 'admin',
            'status' => 'active',
        ];

        return response()->json(['ok' => true, 'user' => $user]);
    }

    #[OA\Delete(
        path: "/settings/users/{id}",
        summary: "Удалить (архивировать) пользователя",
        tags: ["Settings Users"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Пользователь удален"
            )
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        // TODO: Пометить пользователя как удаленного
        // $userId = str_replace('u_', '', $id);
        // $user = AdminUser::findOrFail($userId);
        // $user->deleted = 1;
        // $user->save();
        //
        // ИЛИ использовать soft delete:
        // $user->delete();

        return response()->json(['ok' => true]);
    }
}

