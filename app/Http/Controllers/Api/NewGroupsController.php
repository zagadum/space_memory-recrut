<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "New Groups",
    description: "API для управления формируемыми группами"
)]
class NewGroupsController extends Controller
{
    #[OA\Get(
        path: "/new-groups",
        summary: "Получить список формируемых групп",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Список групп")
        ]
    )]
    public function index(): JsonResponse
    {
        // TODO: Получить из БД
        // $groups = NewGroup::with(['teacher', 'manager', 'students'])->where('status', 'forming')->get();

        $items = [
            [
                'id' => 1,
                'name' => 'Младшая А',
                'type' => 'group',
                'startDate' => '2026-09-01',
                'createdDate' => '2026-03-01',
                'totalSlots' => 12,
                'paid' => 8,
                'manager' => [
                    'name' => 'Магда Ковальска',
                    'initials' => 'МК',
                    'color' => '#4CAF50',
                ],
                'teacher' => [
                    'id' => 1,
                    'name' => 'Анна Новак',
                    'initials' => 'АН',
                    'color' => '#2196F3',
                ],
                'day' => 'Понедельник',
                'time' => '16:00',
                'age' => '7-9',
                'students' => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/new-groups/students",
        summary: "Получить студентов группы",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "groupId",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Список студентов группы")
        ]
    )]
    public function getStudents(Request $request): JsonResponse
    {
        $groupId = $request->query('groupId');

        // TODO: Получить из БД
        // $students = NewGroupStudent::where('new_group_id', $groupId)
        //     ->with('student')
        //     ->get();

        $items = [
            [
                'id' => 1,
                'name' => 'Анна Ковальска',
                'age' => 8,
                'meta' => 'SP14, 3A',
                'contract' => 'signed',
                'paymentStr' => '300 zł · Оплачено',
                'createdDate' => '2026-02-15',
                'manager' => 'Магда',
            ],
            [
                'id' => 2,
                'name' => 'Петр Новак',
                'age' => 9,
                'meta' => 'SP14, 3B',
                'contract' => 'pending',
                'paymentStr' => '300 zł · Ожидается',
                'createdDate' => '2026-02-20',
                'manager' => 'Магда',
            ],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/new-groups/master-students",
        summary: "Получить мастер-список студентов для распределения",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Мастер-список студентов")
        ]
    )]
    public function getMasterStudents(): JsonResponse
    {
        // TODO: Получить студентов без группы или доступных для распределения
        $items = [
            [
                'id' => 100,
                'name' => 'Марта Ковальска',
                'age' => 8,
                'meta' => 'SP14, 2A',
                'initials' => 'МК',
                'color' => '#FF9800',
            ],
            [
                'id' => 101,
                'name' => 'Якуб Новак',
                'age' => 9,
                'meta' => 'SP15, 3B',
                'initials' => 'ЯН',
                'color' => '#9C27B0',
            ],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/new-groups/teachers",
        summary: "Получить список тренеров",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Список тренеров")
        ]
    )]
    public function getTeachers(): JsonResponse
    {
        // TODO: Получить из БД
        // $teachers = Teacher::where('enabled', 1)->where('deleted', 0)->get();

        $items = [
            ['id' => 1, 'name' => 'Анна Новак', 'initials' => 'АН', 'color' => '#2196F3'],
            ['id' => 2, 'name' => 'Петр Ивановски', 'initials' => 'ПИ', 'color' => '#4CAF50'],
            ['id' => 3, 'name' => 'Мария Ковальска', 'initials' => 'МК', 'color' => '#FF9800'],
        ];

        return response()->json(['items' => $items]);
    }

    #[OA\Post(
        path: "/new-groups/create",
        summary: "Создать новую группу",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "type", "day", "time", "startDate"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "type", type: "string", enum: ["group", "individual"]),
                    new OA\Property(property: "day", type: "string"),
                    new OA\Property(property: "time", type: "string"),
                    new OA\Property(property: "startDate", type: "string"),
                    new OA\Property(property: "age", type: "string"),
                    new OA\Property(property: "teacherId", type: "integer"),
                    new OA\Property(property: "studentIds", type: "array", items: new OA\Items(type: "integer"))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Группа создана")
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:group,individual',
            'day' => 'required|string',
            'time' => 'required|string',
            'startDate' => 'required|date',
            'age' => 'nullable|string',
            'teacherId' => 'nullable|integer',
            'studentIds' => 'nullable|array',
            'studentIds.*' => 'integer',
        ]);

        // TODO: Создать группу в БД
        // $group = NewGroup::create([...]);
        // foreach ($validated['studentIds'] as $studentId) {
        //     NewGroupStudent::create([...]);
        // }

        $group = [
            'id' => rand(1000, 9999),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'startDate' => $validated['startDate'],
            'day' => $validated['day'],
            'time' => $validated['time'],
            'age' => $validated['age'] ?? null,
            'teacherId' => $validated['teacherId'] ?? null,
            'students' => $validated['studentIds'] ?? [],
            'createdDate' => now()->format('Y-m-d'),
            'totalSlots' => 12,
            'paid' => 0,
        ];

        return response()->json(['ok' => true, 'group' => $group]);
    }

    #[OA\Post(
        path: "/new-groups/start",
        summary: "Запустить группу (перевести в активные)",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["groupId"],
                properties: [
                    new OA\Property(property: "groupId", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Группа запущена")
        ]
    )]
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'groupId' => 'required|integer',
        ]);

        // TODO: Обновить статус группы на 'active'
        // $group = NewGroup::findOrFail($validated['groupId']);
        // $group->update(['status' => 'active']);

        return response()->json(['ok' => true]);
    }

    #[OA\Post(
        path: "/new-groups/delete",
        summary: "Удалить формируемую группу",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["groupId"],
                properties: [
                    new OA\Property(property: "groupId", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Группа удалена")
        ]
    )]
    public function delete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'groupId' => 'required|integer',
        ]);

        // TODO: Удалить группу
        // $group = NewGroup::findOrFail($validated['groupId']);
        // $group->delete();

        return response()->json(['ok' => true]);
    }

    #[OA\Post(
        path: "/new-groups/add-students",
        summary: "Добавить студентов в группу",
        tags: ["New Groups"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["groupId", "studentIds"],
                properties: [
                    new OA\Property(property: "groupId", type: "integer"),
                    new OA\Property(property: "studentIds", type: "array", items: new OA\Items(type: "integer"))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Студенты добавлены")
        ]
    )]
    public function addStudents(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'groupId' => 'required|integer',
            'studentIds' => 'required|array',
            'studentIds.*' => 'integer',
        ]);

        // TODO: Добавить студентов
        // foreach ($validated['studentIds'] as $studentId) {
        //     NewGroupStudent::firstOrCreate([
        //         'new_group_id' => $validated['groupId'],
        //         'student_id' => $studentId,
        //     ]);
        // }

        $added = count($validated['studentIds']);

        return response()->json(['ok' => true, 'added' => $added]);
    }

    public function removeStudent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'groupId' => 'required|integer',
            'studentName' => 'required|string|max:255',
        ]);

        // TODO: Реализовать фактическое удаление связи студента с группой в БД.
        return response()->json([
            'ok' => true,
            'groupId' => (int) $validated['groupId'],
            'removedStudent' => $validated['studentName'],
        ]);
    }
}

