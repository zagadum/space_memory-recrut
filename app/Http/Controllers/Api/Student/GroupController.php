<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Student Groups",
    description: "API для работы с группами студентов"
)]
class GroupController extends Controller
{
    #[OA\Get(
        path: "/student/groups",
        summary: "Получить группы студента",
        tags: ["Student Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "studentId",
                in: "query",
                required: true,
                description: "ID студента",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "items",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "string"),
                                    new OA\Property(property: "programId", type: "string"),
                                    new OA\Property(property: "programTitle", type: "string"),
                                    new OA\Property(property: "status", type: "string")
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function getGroups(Request $request): JsonResponse
    {
        $studentId = $request->query('studentId');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId is required',
            ], 400);
        }

        // TODO: Реализовать получение групп из БД
        // Пока возвращаем mock данные
        $items = [
            [
                'id' => 'g_sm_a',
                'programId' => 'space',
                'programTitle' => 'Space Memory',
                'programIcon' => '🧠',
                'status' => 'active',
                'subtitle' => 'Занятие #24 личных · Группа A · Пн 16:00',
                'group' => [
                    'code' => 'A',
                    'schedule' => 'Пн 16:00–17:00',
                    'trainer' => 'Анна К.',
                    'place' => 'Маршалковска 10, зал 2',
                    'capacity' => '8 / 12 мест',
                    'stats' => [
                        'total' => 24,
                        'present' => 21,
                        'absent' => 2,
                        'rate' => '87%',
                    ],
                    'trainers' => [
                        [
                            'id' => 't_anna',
                            'name' => 'Анна К.',
                            'role' => 'Основной',
                            'presence' => 'present',
                        ],
                    ],
                ],
            ],
        ];

        return response()->json(['items' => $items], 200);
    }

    #[OA\Post(
        path: "/student/change-group",
        summary: "Изменить группу студента",
        tags: ["Student Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["studentId", "programId", "fromGroup", "toGroup"],
                properties: [
                    new OA\Property(property: "studentId", type: "string"),
                    new OA\Property(property: "programId", type: "string"),
                    new OA\Property(property: "fromGroup", type: "string"),
                    new OA\Property(property: "toGroup", type: "string"),
                    new OA\Property(property: "reason", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешно",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "ok", type: "boolean", example: true)
                    ]
                )
            )
        ]
    )]
    public function changeGroup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'studentId' => 'required|string',
            'programId' => 'required|string',
            'fromGroup' => 'required|string',
            'toGroup' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        // TODO: Реализовать изменение группы в БД

        return response()->json(['ok' => true], 200);
    }

    #[OA\Post(
        path: "/student/trainer-presence",
        summary: "Отметить присутствие тренера",
        tags: ["Student Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["studentId", "groupId", "trainerId", "presence"],
                properties: [
                    new OA\Property(property: "studentId", type: "string"),
                    new OA\Property(property: "groupId", type: "string"),
                    new OA\Property(property: "trainerId", type: "string"),
                    new OA\Property(
                        property: "presence",
                        type: "string",
                        enum: ["present", "absent", "late", "makeup", "empty"]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешно",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "ok", type: "boolean", example: true)
                    ]
                )
            )
        ]
    )]
    public function setTrainerPresence(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'studentId' => 'required|string',
            'groupId' => 'required|string',
            'trainerId' => 'required|string',
            'presence' => 'required|in:present,absent,late,makeup,empty',
        ]);

        // TODO: Реализовать сохранение присутствия тренера в БД

        return response()->json(['ok' => true], 200);
    }
}

