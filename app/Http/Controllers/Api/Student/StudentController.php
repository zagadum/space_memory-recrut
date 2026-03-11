<?php

namespace App\Http\Controllers\Api\Student;

use App\AdminModule\AdminListing;
use App\Http\Controllers\Controller;
use App\Models\Managers;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentNote;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Student Info",
    description: "API для работы с информацией о студентах"
)]
class StudentController extends Controller
{
    #[OA\Get(
        path: "/v1/student/list",
        summary: "Список студентов с пагинацией и фильтрами",
        tags: ["Student Info"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getList(Request $request): JsonResponse
    {
        $this->normalizeBooleanFilters($request, ['without_contact_7_plus', 'only_mine']);

        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'orderBy' => 'nullable|string',
            'orderDirection' => 'nullable|in:asc,desc',
            'sortBy' => 'nullable|string',
            'sortDirection' => 'nullable|in:asc,desc',
            'group_id' => 'nullable|integer|min:1',
            'teacher_id' => 'nullable|integer|min:1',
            'without_contact_7_plus' => 'nullable|boolean',
            'only_mine' => 'nullable|boolean',
        ]);

        $sortMap = [
            'name' => 'full_name',
            'full_name' => 'full_name',
            'start_date' => 'start_date',
            'created_at' => 'start_date',
            'training_term' => 'training_term_days',
            'training_term_days' => 'training_term_days',
            'last_contract' => 'last_contract_date',
            'last_contract_date' => 'last_contract_date',
        ];

        $requestedSort = $validated['orderBy'] ?? $validated['sortBy'] ?? 'full_name';
        $request->merge([
            'orderBy' => $sortMap[$requestedSort] ?? 'full_name',
            'orderDirection' => strtolower((string) ($validated['orderDirection'] ?? $validated['sortDirection'] ?? 'asc')),
            'per_page' => (int) ($validated['per_page'] ?? 20),
            'page' => (int) ($validated['page'] ?? 1),
        ]);

        $listing = AdminListing::create(Student::class)->processRequestAndGet(
            $request,
            [
                'student.id',
                'student.phone',
                'student.created_at as start_date',
                'student.email',
                'student.balance',
                'teacher_groups.id as group_id',
                'teacher_groups.name as group_name',
                'teacher.id as teacher_id',
                DB::raw("CONCAT(IFNULL(teacher.surname, ''), ' ', IFNULL(teacher.first_name, '')) as teacher_name"),
                DB::raw("CONCAT(IFNULL(student.surname, ''), ' ', IFNULL(student.lastname, '')) as full_name"),
                DB::raw('TIMESTAMPDIFF(DAY, student.created_at, NOW()) as training_term_days'),
                DB::raw('payments.last_contract_date as last_contract_date'),
                DB::raw('contacts.last_contact_at as last_contact_at'),
                DB::raw('TIMESTAMPDIFF(DAY, contacts.last_contact_at, NOW()) as days_since_contact'),
            ],
            ['student.surname', 'student.lastname', 'student.email', 'student.phone', 'teacher_groups.name', 'teacher.surname', 'teacher.first_name'],
            function ($query) use ($request) {
                $this->applyBaseStudentListQuery($query, $request);
            }
        );

        if (!$listing instanceof LengthAwarePaginator) {
            return response()->json([
                'data' => $listing,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => count($listing),
                    'total' => count($listing),
                ],
            ]);
        }

        $rows = collect($listing->items())->map(function ($row) {
            $groupName = $row->group_name ?? null;
            $teacherName = trim((string) ($row->teacher_name ?? ''));

            return [
                'id' => (int) $row->id,
                'name' => trim((string) ($row->full_name ?? '')),
                'phone' => $row->phone,
                'email' => $row->email,
                'startDate' => $row->start_date ? date('d.m.Y', strtotime((string) $row->start_date)) : null,
                'startDateRaw' => $row->start_date,
                'trainingTermDays' => (int) ($row->training_term_days ?? 0),
                'lastContract' => $row->last_contract_date ? date('d.m.Y', strtotime((string) $row->last_contract_date)) : null,
                'lastContractRaw' => $row->last_contract_date,
                'lastContact' => $row->last_contact_at ? date('d.m.Y H:i', strtotime((string) $row->last_contact_at)) : null,
                'lastContactRaw' => $row->last_contact_at,
                'daysSinceContact' => $row->days_since_contact !== null ? (int) $row->days_since_contact : null,
                'paid' => ((float) ($row->balance ?? 0)) <= 0,
                'enrollments' => [[
                    'school' => 'Space Memory',
                    'group' => $groupName,
                    'teacher' => $teacherName,
                ]],
                'groupId' => $row->group_id,
                'groupName' => $groupName,
                'teacherId' => $row->teacher_id,
                'teacherName' => $teacherName,
            ];
        })->values();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'current_page' => $listing->currentPage(),
                'last_page' => $listing->lastPage(),
                'per_page' => $listing->perPage(),
                'total' => $listing->total(),
                'from' => $listing->firstItem(),
                'to' => $listing->lastItem(),
            ],
        ]);
    }

    #[OA\Get(
        path: "/v1/student/groups-filter",
        summary: "Список групп для фильтра student/list",
        tags: ["Student Info"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getGroupsFilter(Request $request): JsonResponse
    {
        $query = Student::query();
        $this->applyBaseStudentListQuery($query, $request, true, false);

        $items = $query
            ->select('teacher_groups.id as id', 'teacher_groups.name as name')
            ->whereNotNull('teacher_groups.id')
            ->groupBy('teacher_groups.id', 'teacher_groups.name')
            ->orderBy('teacher_groups.name')
            ->get()
            ->map(static function ($row) {
                return [
                    'id' => (int) $row->id,
                    'name' => $row->name,
                ];
            })
            ->values();

        return response()->json(['items' => $items]);
    }

    #[OA\Get(
        path: "/v1/student/teacher-filter",
        summary: "Список учителей для фильтра student/list",
        tags: ["Student Info"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getTeacherFilter(Request $request): JsonResponse
    {
        $query = Student::query();
        $this->applyBaseStudentListQuery($query, $request, false, true);

        $items = $query
            ->select('teacher.id as id', 'teacher.surname', 'teacher.first_name')
            ->whereNotNull('teacher.id')
            ->groupBy('teacher.id', 'teacher.surname', 'teacher.first_name')
            ->orderBy('teacher.surname')
            ->orderBy('teacher.first_name')
            ->get()
            ->map(static function ($row) {
                return [
                    'id' => (int) $row->id,
                    'name' => trim(($row->surname ?? '') . ' ' . ($row->first_name ?? '')),
                ];
            })
            ->values();

        return response()->json(['items' => $items]);
    }

    private function applyBaseStudentListQuery($query, Request $request, bool $ignoreGroupFilter = false, bool $ignoreTeacherFilter = false): void
    {
        $query->leftJoin('teacher_groups', 'teacher_groups.id', '=', 'student.group_id');
        $query->leftJoin('teacher', 'teacher.id', '=', 'student.teacher_id');

        $query->leftJoinSub(
            DB::table('student_payment')
                ->select('student_id', DB::raw('MAX(date_finish) as last_contract_date'))
                ->groupBy('student_id'),
            'payments',
            'payments.student_id',
            '=',
            'student.id'
        );

        $query->leftJoinSub(
            DB::table('student_notes')
                ->select('student_id', DB::raw('MAX(created_at) as last_contact_at'))
                ->groupBy('student_id'),
            'contacts',
            'contacts.student_id',
            '=',
            'student.id'
        );

        $query->where('student.deleted', 0);
        $query->where('student.blocked', 0);

        $this->applyRoleScope($query, $request);

        if (!$ignoreGroupFilter && (int) $request->input('group_id', 0) > 0) {
            $query->where('student.group_id', (int) $request->input('group_id'));
        }

        if (!$ignoreTeacherFilter && (int) $request->input('teacher_id', 0) > 0) {
            $query->where('student.teacher_id', (int) $request->input('teacher_id'));
        }

        if ((bool) $request->boolean('without_contact_7_plus')) {
            $query->where(function ($subQuery) {
                $subQuery
                    ->whereNull('contacts.last_contact_at')
                    ->orWhere('contacts.last_contact_at', '<', now()->subDays(7));
            });
        }
    }

    private function applyRoleScope($query, Request $request): void
    {
        $jwtRole = (string) $request->attributes->get('user_role', '');
        $jwtUserId = (int) $request->attributes->get('user_id', 0);
        if ($jwtRole === 'teacher' && $jwtUserId > 0) {
            $query->where('student.teacher_id', $jwtUserId);
            return;
        }

        if ($jwtRole === 'franchisee' && $jwtUserId > 0) {
            $query->where('student.franchisee_id', $jwtUserId);
            return;
        }

        if ($jwtRole === 'manager' && $jwtUserId > 0) {
            $manager = Managers::query()->find($jwtUserId);
            if ($manager && isset($manager->franchisee_id) && (int) $manager->franchisee_id > 0) {
                $query->where('student.franchisee_id', (int) $manager->franchisee_id);
                return;
            }
        }

        // Флаг only_mine пока no-op: явной связи "мой студент" в текущей схеме нет.
    }

    private function normalizeBooleanFilters(Request $request, array $keys): void
    {
        $normalized = [];

        foreach ($keys as $key) {
            if (!$request->has($key)) {
                continue;
            }

            $value = $request->input($key);

            if ($value === '' || $value === null) {
                $normalized[$key] = null;
                continue;
            }

            if (is_bool($value)) {
                $normalized[$key] = $value;
                continue;
            }

            if (is_int($value)) {
                $normalized[$key] = $value === 1;
                continue;
            }

            $stringValue = strtolower(trim((string) $value));

            if (in_array($stringValue, ['1', 'true', 'yes', 'on'], true)) {
                $normalized[$key] = true;
                continue;
            }

            if (in_array($stringValue, ['0', 'false', 'no', 'off'], true)) {
                $normalized[$key] = false;
                continue;
            }

            // Если пришло неожидаемое значение, оставляем null,
            // чтобы не ломать list из-за одного фильтра.
            $normalized[$key] = null;
        }

        if (!empty($normalized)) {
            $request->merge($normalized);
        }
    }

    #[OA\Get(
        path: "/student/info",
        summary: "Получить информацию о студенте",
        tags: ["Student Info"],
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
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getInfo(Request $request): JsonResponse
    {
        $studentId = $request->query('studentId');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId is required',
            ], 400);
        }

        // TODO: Получить реальные данные из БД
        $info = [
            'child' => [
                'fullName' => 'Анна Ковальска',
                'birthDate' => '15.04.2014',
                'age' => '11',
                'school' => 'SP nr 14 im. Staszica, Warszawa',
                'className' => '5A',
            ],
            'parent' => [
                'fullName' => 'Ewa Kowalska',
                'phone' => '+48 601 234 567',
                'email' => 'ewa.kowalska@gmail.com',
            ],
            'billing' => [
                'address' => 'ul. Nowy Świat 45/12, 00-042 Warszawa',
                'nip' => '123-456-78-90',
                'clientType' => 'person',
            ],
            'rodo' => [
                [
                    'id' => 'r1',
                    'title' => 'Обработка персональных данных (RODO)',
                    'date' => '12.01.2025',
                    'status' => 'signed',
                ],
            ],
            'source' => [
                'channel' => 'Рекомендация',
                'note' => 'Пришли от подруги (клиент INDIGO) — скидка по рефералу.',
            ],
        ];

        return response()->json(['info' => $info], 200);
    }

    #[OA\Post(
        path: "/student/info",
        summary: "Обновить информацию о студенте",
        tags: ["Student Info"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["studentId", "patch"],
                properties: [
                    new OA\Property(property: "studentId", type: "string"),
                    new OA\Property(property: "patch", type: "object")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Успешно")
        ]
    )]
    public function updateInfo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'studentId' => 'required|string',
            'patch' => 'required|array',
        ]);

        // TODO: Обновить информацию в БД

        return response()->json([
            'ok' => true,
            'info' => $validated['patch'],
        ], 200);
    }

    #[OA\Get(
        path: "/student/attendance",
        summary: "Получить информацию о посещаемости",
        tags: ["Student Attendance"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "studentId",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getAttendance(Request $request): JsonResponse
    {
        $studentId = $this->normalizeStudentId($request->query('studentId'));

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId is required',
            ], 400);
        }

        $teacherColumn = $this->attendanceTeacherColumn();

        $items = StudentAttendance::query()
            ->where('student_id', $studentId)
            ->orderByDesc('lesson_date')
            ->orderByDesc('lesson_num')
            ->orderByDesc('id')
            ->get();

        $teacherIds = $items
            ->pluck($teacherColumn)
            ->filter()
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values();

        $teachersById = $teacherIds->isEmpty()
            ? collect()
            : DB::table('teacher')
                ->whereIn('id', $teacherIds)
                ->get(['id', 'surname', 'first_name'])
                ->keyBy('id');

        $presentCount = $items->where('mark', 'present')->count();
        $absentCount = $items->where('mark', 'absent')->count();
        $makeupCount = $items->where('mark', 'makeup')->count();
        $lateCount = $items->where('mark', 'late')->count();
        $totalCount = $items->count();

        $attendance = [
            'summary' => [
                'total' => $totalCount,
                'present' => $presentCount,
                'absent' => $absentCount,
                'makeup' => $makeupCount,
                'late' => $lateCount,
                'rate' => $totalCount > 0 ? round((($presentCount + $lateCount) / $totalCount) * 100, 1) : 0,
            ],
            'items' => $items->map(function (StudentAttendance $item) use ($teacherColumn, $teachersById) {
                $teacherId = (int) ($item->{$teacherColumn} ?? 0);
                $teacher = $teacherId > 0 ? $teachersById->get($teacherId) : null;
                $teacherName = trim(((string) ($teacher->surname ?? '')) . ' ' . ((string) ($teacher->first_name ?? '')));

                return $this->formatAttendanceRow($item, $teacherName);
            })->values(),
        ];

        return response()->json(['attendance' => $attendance], 200);
    }

    #[OA\Post(
        path: "/student/attendance",
        summary: "Отметить посещаемость",
        tags: ["Student Attendance"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["studentId", "attendanceId", "mark"],
                properties: [
                    new OA\Property(property: "studentId", type: "string"),
                    new OA\Property(property: "attendanceId", type: "string"),
                    new OA\Property(
                        property: "mark",
                        type: "string",
                        enum: ["present", "absent", "late", "makeup", "empty"]
                    ),
                    new OA\Property(property: "note", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Успешно")
        ]
    )]
    public function setAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'studentId' => 'required|string',
            'attendanceId' => 'required|string',
            'mark' => 'required|in:present,absent,late,makeup,empty',
            'note' => 'nullable|string',
        ]);

        $studentId = $this->normalizeStudentId($validated['studentId']);
        $attendanceId = $this->normalizeAttendanceId($validated['attendanceId']);

        if (!$studentId || !$attendanceId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId and attendanceId are required',
            ], 400);
        }

        $teacherColumn = $this->attendanceTeacherColumn();

        $attendance = StudentAttendance::query()
            ->where('student_id', $studentId)
            ->find($attendanceId);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);
        }

        $attendance->mark = $validated['mark'];
        $attendance->note = trim((string) ($validated['note'] ?? '')) ?: null;
        if (!$attendance->created_by) {
            $attendance->created_by = $this->resolveCurrentAdminId($request);
        }
        $attendance->save();

        $teacherName = '';
        $teacherId = (int) ($attendance->{$teacherColumn} ?? 0);
        if ($teacherId > 0) {
            $teacher = DB::table('teacher')->where('id', $teacherId)->first(['surname', 'first_name']);
            $teacherName = trim(((string) ($teacher->surname ?? '')) . ' ' . ((string) ($teacher->first_name ?? '')));
        }

        return response()->json([
            'ok' => true,
            'row' => $this->formatAttendanceRow($attendance, $teacherName),
        ], 200);
    }

    #[OA\Get(
        path: "/student/progress",
        summary: "Получить информацию о прогрессе",
        tags: ["Student Progress"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "studentId",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getProgress(Request $request): JsonResponse
    {
        $studentId = $request->query('studentId');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId is required',
            ], 400);
        }

        // TODO: Получить данные из student_progress
        $progress = [
            'kpi' => [
                [
                    'id' => 'p1',
                    'title' => 'Скорость запоминания',
                    'value' => '×5',
                    'hint' => 'февраль 2026',
                ],
                [
                    'id' => 'p2',
                    'title' => 'Точность',
                    'value' => '92%',
                    'hint' => 'последние 4 занятия',
                ],
            ],
            'achievements' => [
                [
                    'id' => 'ach1',
                    'title' => '80 карт за занятие',
                    'date' => '10.02.2026',
                ],
                [
                    'id' => 'ach2',
                    'title' => 'Блок 3 — зачёт',
                    'date' => '17.02.2026',
                ],
            ],
        ];

        return response()->json(['progress' => $progress], 200);
    }

    #[OA\Get(
        path: "/student/notes",
        summary: "Получить заметки о студенте",
        tags: ["Student Notes"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "studentId",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Успешный ответ")
        ]
    )]
    public function getNotes(Request $request): JsonResponse
    {
        $studentId = $this->normalizeStudentId($request->query('studentId'));

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId is required',
            ], 400);
        }

        $items = StudentNote::query()
            ->with('creator:id,first_name,last_name')
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (StudentNote $note) => $this->formatStudentNote($note))
            ->values();

        return response()->json(['items' => $items], 200);
    }

    #[OA\Post(
        path: "/student/notes",
        summary: "Создать новую заметку",
        tags: ["Student Notes"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["studentId", "text"],
                properties: [
                    new OA\Property(property: "studentId", type: "string"),
                    new OA\Property(property: "type", type: "string"),
                    new OA\Property(property: "direction", type: "string"),
                    new OA\Property(property: "category", type: "string"),
                    new OA\Property(property: "status", type: "string"),
                    new OA\Property(property: "tags", type: "array", items: new OA\Items(type: "string")),
                    new OA\Property(property: "text", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Успешно")
        ]
    )]
    public function createNote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'studentId' => 'required|string',
            'type' => 'nullable|in:call,email,note,meet',
            'direction' => 'nullable|string',
            'category' => 'nullable|string',
            'status' => 'nullable|in:open,done,closed',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'text' => 'required|string',
        ]);

        $studentId = $this->normalizeStudentId($validated['studentId']);
        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'studentId is required',
            ], 400);
        }

        if (!Student::query()->whereKey($studentId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found',
            ], 404);
        }

        $note = StudentNote::query()->create([
            'student_id' => $studentId,
            'type' => $this->normalizeNoteType($validated['type'] ?? null),
            'direction' => $validated['direction'] ?? null,
            'status' => $validated['status'] ?? 'open',
            'category' => $validated['category'] ?? 'general',
            'title' => $this->buildNoteTitle($validated['type'] ?? null, $validated['direction'] ?? null),
            'text' => trim((string) $validated['text']),
            'tags' => array_values(array_filter($validated['tags'] ?? [], static fn ($tag) => trim((string) $tag) !== '')),
            'created_by' => $this->resolveCurrentAdminId($request),
        ]);

        $note->load('creator:id,first_name,last_name');

        return response()->json([
            'ok' => true,
            'note' => $this->formatStudentNote($note),
        ], 200);
    }

    #[OA\Patch(
        path: "/student/notes/{noteId}",
        summary: "Обновить заметку",
        tags: ["Student Notes"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "noteId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "studentId", type: "string"),
                    new OA\Property(property: "type", type: "string"),
                    new OA\Property(property: "direction", type: "string"),
                    new OA\Property(property: "category", type: "string"),
                    new OA\Property(property: "status", type: "string"),
                    new OA\Property(property: "tags", type: "array", items: new OA\Items(type: "string")),
                    new OA\Property(property: "text", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Успешно")
        ]
    )]
    public function updateNote(Request $request, string $noteId): JsonResponse
    {
        $validated = $request->validate([
            'studentId' => 'nullable|string',
            'type' => 'nullable|in:call,email,note,meet',
            'direction' => 'nullable|string',
            'category' => 'nullable|string',
            'status' => 'nullable|in:open,done,closed',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'text' => 'nullable|string',
        ]);

        $normalizedNoteId = $this->normalizeNoteId($noteId);
        if (!$normalizedNoteId) {
            return response()->json([
                'success' => false,
                'message' => 'noteId is required',
            ], 400);
        }

        $note = StudentNote::query()->with('creator:id,first_name,last_name')->find($normalizedNoteId);
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }

        if (array_key_exists('studentId', $validated)) {
            $studentId = $this->normalizeStudentId($validated['studentId']);
            if (!$studentId || (int) $note->student_id !== $studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student mismatch',
                ], 422);
            }
        }

        if (array_key_exists('type', $validated)) {
            $note->type = $this->normalizeNoteType($validated['type']);
        }
        if (array_key_exists('direction', $validated)) {
            $note->direction = $validated['direction'];
        }
        if (array_key_exists('category', $validated)) {
            $note->category = $validated['category'] ?? 'general';
        }
        if (array_key_exists('status', $validated)) {
            $note->status = $validated['status'] ?? 'open';
        }
        if (array_key_exists('text', $validated) && $validated['text'] !== null) {
            $note->text = trim((string) $validated['text']);
        }
        if (array_key_exists('tags', $validated)) {
            $note->tags = array_values(array_filter($validated['tags'] ?? [], static fn ($tag) => trim((string) $tag) !== ''));
        }

        $note->title = $this->buildNoteTitle($note->type, $note->direction);
        $note->save();
        $note->refresh()->load('creator:id,first_name,last_name');

        return response()->json([
            'ok' => true,
            'note' => $this->formatStudentNote($note),
        ], 200);
    }

    #[OA\Delete(
        path: "/student/notes/{noteId}",
        summary: "Удалить заметку",
        tags: ["Student Notes"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "noteId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Успешно")
        ]
    )]
    public function deleteNote(string $noteId): JsonResponse
    {
        $normalizedNoteId = $this->normalizeNoteId($noteId);
        if (!$normalizedNoteId) {
            return response()->json([
                'success' => false,
                'message' => 'noteId is required',
            ], 400);
        }

        $note = StudentNote::query()->find($normalizedNoteId);
        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }

        $note->delete();

        return response()->json(['ok' => true], 200);
    }

    private function normalizeStudentId(mixed $studentId): ?int
    {
        if ($studentId === null || $studentId === '') {
            return null;
        }

        if (is_int($studentId)) {
            return $studentId > 0 ? $studentId : null;
        }

        if (is_string($studentId) && preg_match('/(\d+)/', $studentId, $matches) === 1) {
            $normalized = (int) $matches[1];
            return $normalized > 0 ? $normalized : null;
        }

        return null;
    }

    private function normalizeNoteId(mixed $noteId): ?int
    {
        if ($noteId === null || $noteId === '') {
            return null;
        }

        if (is_int($noteId)) {
            return $noteId > 0 ? $noteId : null;
        }

        if (is_string($noteId) && preg_match('/(\d+)/', $noteId, $matches) === 1) {
            $normalized = (int) $matches[1];
            return $normalized > 0 ? $normalized : null;
        }

        return null;
    }

    private function normalizeAttendanceId(mixed $attendanceId): ?int
    {
        if ($attendanceId === null || $attendanceId === '') {
            return null;
        }

        if (is_int($attendanceId)) {
            return $attendanceId > 0 ? $attendanceId : null;
        }

        if (is_string($attendanceId) && preg_match('/(\d+)/', $attendanceId, $matches) === 1) {
            $normalized = (int) $matches[1];
            return $normalized > 0 ? $normalized : null;
        }

        return null;
    }

    private function attendanceTeacherColumn(): string
    {
        return Schema::hasColumn('student_attendance', 'teacher_id') ? 'teacher_id' : 'trainer_id';
    }

    private function formatAttendanceRow(StudentAttendance $attendance, string $teacherName = ''): array
    {
        if ($teacherName === '') {
            $teacherColumn = $this->attendanceTeacherColumn();
            $teacherId = (int) ($attendance->{$teacherColumn} ?? 0);
            if ($teacherId > 0) {
                $teacher = DB::table('teacher')->where('id', $teacherId)->first(['surname', 'first_name']);
                $teacherName = trim(((string) ($teacher->surname ?? '')) . ' ' . ((string) ($teacher->first_name ?? '')));
            }
        }

        return [
            'id' => 'a' . $attendance->id,
            'num' => (int) $attendance->lesson_num,
            'date' => optional($attendance->lesson_date)->format('d.m.Y'),
            'topic' => (string) ($attendance->topic ?? ''),
            'trainer' => $teacherName,
            'teacher' => $teacherName,
            'mark' => (string) $attendance->mark,
            'note' => (string) ($attendance->note ?? ''),
        ];
    }

    private function normalizeNoteType(?string $type): string
    {
        return match ($type) {
            'call', 'email', 'note' => $type,
            'meet' => 'note',
            default => 'note',
        };
    }

    private function buildNoteTitle(?string $type, ?string $direction): string
    {
        $direction = trim((string) $direction);
        if ($direction !== '') {
            return $direction;
        }

        return match ($type) {
            'call' => 'Входящий звонок',
            'email' => 'Исходящий email',
            'meet' => 'Встреча',
            default => 'Заметка',
        };
    }

    private function resolveCurrentAdminId(Request $request): ?int
    {
        $jwtUserId = (int) $request->attributes->get('user_id', 0);
        if ($jwtUserId > 0 && DB::table('admin_users')->where('id', $jwtUserId)->exists()) {
            return $jwtUserId;
        }

        $authId = Auth::guard('admin')->id() ?? Auth::id();
        $authId = (int) $authId;

        if ($authId > 0 && DB::table('admin_users')->where('id', $authId)->exists()) {
            return $authId;
        }

        return null;
    }

    private function formatStudentNote(StudentNote $note): array
    {
        $creatorName = trim((string) optional($note->creator)->full_name);

        return [
            'id' => 'n' . $note->id,
            'type' => $note->type,
            'status' => $note->status,
            'category' => $note->category ?: 'general',
            'who' => $creatorName !== '' ? $creatorName : 'System',
            'when' => optional($note->created_at)->format('d.m.Y · H:i'),
            'title' => $note->title ?: $this->buildNoteTitle($note->type, $note->direction),
            'text' => $note->text,
            'tags' => array_values($note->tags ?? []),
        ];
    }
}

