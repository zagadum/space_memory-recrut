<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Models\GlsPaymentCharge;
use App\Models\GlsPaymentTransaction;
use App\Models\GlsProject;
use App\Models\GlsLessonAdditional;
use App\Models\GlsInvoiceDocument;
use App\Models\Student;
use App\Repositories\Payment\PaymentDocumentRepository;
use App\Repositories\Payment\PaymentReadRepository;
use App\Repositories\Payment\StudentProgramEventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Payments",
    description: "API для работы с платежами студентов"
)]
class PaymentController extends Controller
{
    private PaymentReadRepository $paymentReadRepository;
    private PaymentDocumentRepository $paymentDocumentRepository;
    private StudentProgramEventRepository $programEventRepository;

    public function __construct(
        PaymentReadRepository $paymentReadRepository,
        PaymentDocumentRepository $paymentDocumentRepository,
        StudentProgramEventRepository $programEventRepository
    ) {
        $this->paymentReadRepository = $paymentReadRepository;
        $this->paymentDocumentRepository = $paymentDocumentRepository;
        $this->programEventRepository = $programEventRepository;
    }

    #[OA\Get(
        path: "/v1/payments/student/{id}",
        summary: "Получить платежи студента",
        tags: ["Payments"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID студента",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "OK"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "studentId", type: "integer", example: 123),
                                new OA\Property(property: "payments", type: "array", items: new OA\Items(type: "object"))
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Студент не найден"
            )
        ]
    )]
    public function getStudentPayments(int $id): JsonResponse
    {
        $student = Student::find($id);
        if (!$student) {
            return $this->errorResponse('Student not found', 404);
        }

        // Формируем профиль студента
        $dob = null;
        if ($student->dob) {
            $dob = $student->dob instanceof \Carbon\Carbon ? $student->dob : \Carbon\Carbon::parse($student->dob);
        }

        $dateFinish = null;
        if ($student->date_finish) {
            $dateFinish = $student->date_finish instanceof \Carbon\Carbon ? $student->date_finish : \Carbon\Carbon::parse($student->date_finish);
        }

        $profile = [
            'id' => (string) $student->id,
            'initials' => mb_substr($student->surname ?? '', 0, 1) . mb_substr($student->lastname ?? '', 0, 1),
            'name' => trim(($student->surname ?? '') . ' ' . ($student->lastname ?? '')),
            'firstName' => $student->lastname ?? '',
            'lastName' => $student->surname ?? '',
            'email' => $student->email ?? '',
            'birthDate' => $dob ? $dob->format('Y-m-d') : '',
            'age' => $dob ? now()->diffInYears($dob) : 0,
            'phone' => $student->parent1_phone ?? $student->parent_phone ?? '',
            'parentName' => trim(($student->parent1_lastname ?? '') . ' ' . ($student->parent1_surname ?? '')),
            'parentFirstName' => $student->parent1_lastname ?? $student->parent_name ?? '',
            'parentLastName' => $student->parent1_surname ?? $student->parent_surname ?? '',
            'parentPhone' => $student->parent1_phone ?? $student->parent_phone ?? '',
            'country' => $student->country ?? 'PL',
            'city' => $student->city ?? '',
            'street' => $student->address ?? '',
            'apartment' => $student->apartment ?? '',
            'postalCode' => $student->zip ?? '',
            'parentRole' => 'родитель',
            'parentPassport' => $student->parent_passport ?? '',
            'status' => $student->blocked ? 'inactive' : 'active',
            'statusColor' => $student->blocked ? 'gray' : 'green',
            'photoConsent' => (bool) ($student->photo_consent ?? false),
            'regComment' => $student->reg_comment ?? '',
            'totalBalance' => [
                'value' => number_format($student->balance ?? 0, 2, '.', ''),
                'label' => ($student->balance ?? 0) >= 0 ? 'active' : 'debt',
                'color' => ($student->balance ?? 0) >= 0 ? 'green' : 'red',
            ],
            'nextPay' => [
                'date' => $dateFinish ? $dateFinish->format('Y-m-d') : '',
                'approx' => '',
            ],
            'enrollments' => [],
        ];

        // Формируем программы для фронта в контракте memory-adm
        // (основная + indigo + extras)
        $programs = $this->buildProgramsForFrontend($student);

        return $this->successResponse([
            'student' => $profile,
            'programs' => $programs,
        ]);
    }

    private function formatProgramsFromPayments($student, $payments): array
    {
        // Загружаем группу и учителя
        $group = \App\Models\TeacherGroup::find($student->group_id);
        $teacher = \App\Models\Teacher::find($student->teacher_id);

        // Определяем программу и иконку по названию группы
        $programInfo = $this->detectProgramType($group ? $group->name : ''); //@todo фигня убрать

        // Формируем помесячную сетку платежей
        $years = [];
        if ($payments->isNotEmpty()) {
            foreach ($payments as $payment) {
                if (!$payment->date_pay) continue;

                $datePay = $payment->date_pay instanceof \Carbon\Carbon ? $payment->date_pay : \Carbon\Carbon::parse($payment->date_pay);

                $year = $datePay->format('Y');
                $month = (int) $datePay->format('n') - 1; // 0-11 для JS

                if (!isset($years[$year])) {
                    $years[$year] = array_fill(0, 12, [
                        's' => 'future',
                        'a' => 0,
                        'ksef' => null,
                        'g1' => 0,
                        'g2' => 0,
                    ]);
                }

                $years[$year][$month] = [
                    's' => $payment->enabled ? 'paid' : 'pending',
                    'payStatus' => $payment->enabled ? 'paid' : 'pending',
                    'a' => (float) $payment->sum_aboniment,
                    'ksef' => $payment->enabled ? 'ok' : null,
                    'g1' => 4, // Количество занятий в месяц
                    'g2' => 0,
                    'txDate' => $datePay->format('Y-m-d'),
                ];
            }
        }

        // Формируем название и описание программы
        $programName = $programInfo['icon'] . ' ' . $programInfo['name'];
        $programSub = $this->buildProgramSubtitle($group, $teacher, $student);

        return [[
            'id' => 'prog_' . $student->id,
            'name' => $programName,
            'sub' => $programSub,
            'tariff' => (float) ($student->sum_aboniment ?? 0),
            'balance' => (float) ($student->balance ?? 0),
            'balanceLabel' => ($student->balance ?? 0) >= 0 ? 'active' : 'debt',
            'barGradient' => $programInfo['gradient'],
            'years' => $years,
            'transactions' => [],
            'extras' => [],
        ]];
    }

    /**
     * Определяет тип программы по названию группы
     */
    private function detectProgramType(?string $groupName): array
    {
        if (!$groupName) {
            return [
                'name' => 'Основная программа',
                'icon' => '📚',
                'gradient' => 'linear-gradient(180deg, #10b981, #059669)',
            ];
        }

        $groupName = mb_strtolower($groupName);

        // Space Memory - программа развития памяти
        if (str_contains($groupName, 'memory') || str_contains($groupName, 'память')) {
            return [
                'name' => 'Space Memory',
                'icon' => '🌌',
                'gradient' => 'linear-gradient(180deg, #3b82f6, #8b5cf6)',
            ];
        }

        // Speedy Mind / Mental Arithmetic - ментальная арифметика
        if (str_contains($groupName, 'speedy') || str_contains($groupName, 'indigo')) {
            return [
                'name' => 'Speedy Mind',
                'icon' => '⚡',
                'gradient' => 'linear-gradient(180deg, #8b5cf6, #ec4899)',
            ];
        }




        // По умолчанию
        return [
            'name' => 'Основная программа',
            'icon' => '📚',
            'gradient' => 'linear-gradient(180deg, #10b981, #059669)',
        ];
    }

    /**
     * Формирует подзаголовок программы с информацией о группе и учителе
     */
    private function buildProgramSubtitle($group, $teacher, $student): string
    {
        $parts = [];

        // Название группы
        if ($group && $group->name) {
            $parts[] = $group->name;
        }

        // Расписание
        if ($group && $group->start_time) {
            $time = \Carbon\Carbon::parse($group->start_time);
            $weekDay = $this->getWeekDayFromGroup($group);
            if ($weekDay) {
                $parts[] = $weekDay . ' ' . $time->format('H:i');
            }
        }

        // Учитель
        if ($teacher) {
            $teacherName = trim(($teacher->surname ?? '') . ' ' . ($teacher->first_name ?? ''));
            if ($teacherName) {
                $parts[] = $teacherName;
            }
        }

        // Тариф
        if ($student->sum_aboniment) {
            $currency = $student->country === 'UA' ? 'грн/мес' : 'zł/мес';
            $parts[] = $student->sum_aboniment . ' ' . $currency;
        }

        // Скидка (если balance положительный - возможно есть скидка)
        if (($student->discount ?? 0) > 0) {
            $parts[] = '−' . $student->discount . '% скидка';
        } else {
            $parts[] = 'без скидки';
        }

        return implode(' · ', array_filter($parts));
    }

    /**
     * Получает день недели из расписания группы
     */
    private function getWeekDayFromGroup($group): ?string
    {
        if (!$group) return null;

        $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        // Проверяем какие дни недели активны
        for ($i = 1; $i <= 7; $i++) {
            $field = 'workday' . $i;
            if (isset($group->$field) && $group->$field) {
                return $weekDays[$i - 1];
            }
        }

        return null;
    }

    #[OA\Get(
        path: "/v1/payments/transactions",
        summary: "Получить транзакции программы",
        tags: ["Payments"],
        parameters: [
            new OA\Parameter(
                name: "programId",
                in: "query",
                required: true,
                description: "ID программы",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "OK"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "programId", type: "integer", example: 12),
                                new OA\Property(property: "transactions", type: "array", items: new OA\Items(type: "object"))
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Некорректный programId"
            )
        ]
    )]
    public function getTransactions(Request $request): JsonResponse
    {
        $programId = (int) $request->query('programId');
        if ($programId <= 0) {
            return $this->errorResponse('programId is required', 422);
        }

        $transactions = $this->paymentReadRepository->getTransactions($programId);

        return $this->successResponse([
            'programId' => $programId,
            'transactions' => $transactions,
        ]);
    }

    #[OA\Get(
        path: "/v1/payments/ksef-invoices",
        summary: "Получить счета KSEF",
        tags: ["Payments"],
        parameters: [
            new OA\Parameter(
                name: "programId",
                in: "query",
                required: true,
                description: "ID программы",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Успешный ответ",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "OK"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "programId", type: "integer", example: 12),
                                new OA\Property(property: "invoices", type: "array", items: new OA\Items(type: "object"))
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Некорректный programId"
            )
        ]
    )]
    public function getKsefInvoices(Request $request): JsonResponse
    {
        $programId = (int) $request->query('programId');
        if ($programId <= 0) {
            return $this->errorResponse('programId is required', 422);
        }

        $invoices = $this->paymentReadRepository->getKsefInvoices($programId);

        return $this->successResponse([
            'programId' => $programId,
            'invoices' => $invoices,
        ]);
    }

    #[OA\Post(
        path: "/v1/payments/refund",
        summary: "Создать возврат средств",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["fvnum", "amount", "reason", "method"],
                properties: [
                    new OA\Property(property: "fvnum", type: "string", maxLength: 64, example: "FV/2024/001"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: 100.50),
                    new OA\Property(property: "reason", type: "string", maxLength: 1000, example: "Возврат по требованию клиента"),
                    new OA\Property(property: "method", type: "string", maxLength: 64, example: "bank_transfer"),
                    new OA\Property(property: "iban", type: "string", maxLength: 64, nullable: true, example: "PL61109010140000071219812874")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Возврат создан",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Refund request accepted"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(
                                    property: "refund",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer"),
                                        new OA\Property(property: "programId", type: "integer"),
                                        new OA\Property(property: "fvnum", type: "string"),
                                        new OA\Property(property: "amount", type: "number"),
                                        new OA\Property(property: "reason", type: "string"),
                                        new OA\Property(property: "method", type: "string"),
                                        new OA\Property(property: "iban", type: "string", nullable: true),
                                        new OA\Property(property: "status", type: "string")
                                    ],
                                    type: "object"
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function submitRefund(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fvnum' => ['required', 'string', 'max:64'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:1000'],
            'method' => ['required', 'string', 'max:64'],
            'iban' => ['nullable', 'string', 'max:64'],
        ]);

        $invoice = $this->paymentDocumentRepository->findInvoiceByNumber($validated['fvnum']);
        if (!$invoice) {
            return $this->errorResponse('Invoice not found for fvnum', 422);
        }

        $refund = $this->paymentDocumentRepository->createRefund([
            'programId' => (int) $invoice->student_id,
            'fvnum' => $validated['fvnum'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'method' => $validated['method'],
            'iban' => $validated['iban'] ?? null,
        ]);

        $this->programEventRepository->create([
            'student_id' => (int) $invoice->student_id,
            'event_type' => 'refund',
            'effective_date' => now()->toDateString(),
            'reason' => $validated['reason'],
            'comment' => 'fvnum: ' . $validated['fvnum'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'refund' => [
                'id' => (int) $refund->id,
                'programId' => (int) $invoice->student_id,
                'fvnum' => $validated['fvnum'],
                'amount' => (float) $validated['amount'],
                'reason' => $validated['reason'],
                'method' => $validated['method'],
                'iban' => $validated['iban'] ?? null,
                'status' => $refund->status,
            ],
        ], 'Refund request accepted');
    }

    #[OA\Post(
        path: "/v1/payments/invoice",
        summary: "Редактировать счет",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "fvnum", "issueDate", "payDate", "amount"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "fvnum", type: "string", maxLength: 64, example: "FV/2024/001"),
                    new OA\Property(property: "issueDate", type: "string", format: "date", example: "2024-01-15"),
                    new OA\Property(property: "payDate", type: "string", format: "date", example: "2024-01-30"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: 150.00)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Счет обновлен"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function editInvoice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'fvnum' => ['required', 'string', 'max:64'],
            'issueDate' => ['required', 'date'],
            'payDate' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $invoice = $this->paymentDocumentRepository->upsertInvoice($validated);

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'invoice',
            'effective_date' => $validated['issueDate'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'invoice' => [
                'id' => (int) $invoice->id,
                'programId' => (int) $validated['programId'],
                'fvnum' => $validated['fvnum'],
                'issueDate' => $validated['issueDate'],
                'payDate' => $validated['payDate'],
                'amount' => (float) $validated['amount'],
                'status' => $invoice->status,
            ],
        ], 'Invoice updated');
    }

    #[OA\Post(
        path: "/v1/payments/correction",
        summary: "Создать корректировку платежа",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "amount", "note", "corrDate"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "amount", type: "number", format: "float", example: -50.00),
                    new OA\Property(property: "note", type: "string", maxLength: 1000, example: "Корректировка переплаты"),
                    new OA\Property(property: "corrDate", type: "string", format: "date", example: "2024-01-20")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Корректировка сохранена"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function submitCorrection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'amount' => ['required', 'numeric'],
            'note' => ['required', 'string', 'max:1000'],
            'corrDate' => ['required', 'date'],
        ]);

        $correction = $this->paymentDocumentRepository->createCorrection($validated);

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'correction',
            'effective_date' => $validated['corrDate'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'correction' => [
                'id' => (int) $correction->id,
                'programId' => (int) $validated['programId'],
                'amount' => (float) $validated['amount'],
                'note' => $validated['note'],
                'corrDate' => $validated['corrDate'],
                'status' => $correction->status,
            ],
        ], 'Correction saved');
    }

    #[OA\Post(
        path: "/v1/payments/tariff",
        summary: "Изменить тариф программы",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "value", "fromMonthIndex"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "value", type: "number", format: "float", example: 200.00),
                    new OA\Property(property: "fromMonthIndex", type: "integer", example: 3)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Тариф изменен"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function changeTariff(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'value' => ['required', 'numeric', 'min:0'],
            'fromMonthIndex' => ['required', 'integer', 'min:0'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->sum_aboniment = $validated['value'];
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'tariff',
            'from_month_index' => (int) $validated['fromMonthIndex'],
            'value' => $validated['value'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'programId' => (int) $validated['programId'],
            'value' => (float) $validated['value'],
            'fromMonthIndex' => (int) $validated['fromMonthIndex'],
        ], 'Tariff changed');
    }

    #[OA\Post(
        path: "/v1/payments/pause",
        summary: "Приостановить программу",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "from", "to", "reason"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "from", type: "string", format: "date", example: "2024-02-01"),
                    new OA\Property(property: "to", type: "string", format: "date", example: "2024-03-01"),
                    new OA\Property(property: "reason", type: "string", maxLength: 255, example: "Отпуск"),
                    new OA\Property(property: "comment", type: "string", maxLength: 1000, nullable: true, example: "По заявлению студента")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Программа приостановлена"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function setPause(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'reason' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->blocked = 1;
        $student->blocking_reason = trim($validated['reason'] . ' ' . ($validated['comment'] ?? ''));
        $student->date_finish = $validated['to'];
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'pause',
            'effective_date' => $validated['from'],
            'reason' => $validated['reason'],
            'comment' => $validated['comment'] ?? null,
            'payload' => $validated,
        ]);

        return $this->successResponse($validated, 'Program paused');
    }

    #[OA\Post(
        path: "/v1/payments/discount",
        summary: "Установить скидку",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "kind", "value", "fromMonthIndex"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "kind", type: "string", maxLength: 64, example: "family"),
                    new OA\Property(property: "value", type: "number", format: "float", example: 10.00),
                    new OA\Property(property: "fromMonthIndex", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Скидка установлена"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function setDiscount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'kind' => ['required', 'string', 'max:64'],
            'value' => ['required', 'numeric'],
            'fromMonthIndex' => ['required', 'integer', 'min:0'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->discount = $validated['value'];
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'discount',
            'from_month_index' => (int) $validated['fromMonthIndex'],
            'value' => $validated['value'],
            'comment' => $validated['kind'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'programId' => (int) $validated['programId'],
            'kind' => $validated['kind'],
            'value' => (float) $validated['value'],
            'fromMonthIndex' => (int) $validated['fromMonthIndex'],
        ], 'Discount set');
    }

    #[OA\Post(
        path: "/v1/payments/extra",
        summary: "Добавить дополнительную оплату",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "date", "title", "amount"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "date", type: "string", format: "date", example: "2024-01-25"),
                    new OA\Property(property: "title", type: "string", maxLength: 255, example: "Дополнительные материалы"),
                    new OA\Property(property: "amount", type: "number", format: "float", example: 50.00)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Дополнительная оплата добавлена"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function addExtra(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
        ]);

        $extra = $this->paymentDocumentRepository->createExtra($validated);

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'extra',
            'effective_date' => $validated['date'],
            'value' => $validated['amount'],
            'comment' => $validated['title'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'extra' => [
                'id' => (int) $extra->id,
                'programId' => (int) $validated['programId'],
                'date' => $validated['date'],
                'title' => $validated['title'],
                'amount' => (float) $validated['amount'],
            ],
        ], 'Extra charge added');
    }

    #[OA\Post(
        path: "/v1/payments/unlock",
        summary: "Разблокировать программу",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Программа разблокирована"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function unlock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->blocked = 0;
        $student->blocking_reason = null;
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'unlock',
            'effective_date' => now()->toDateString(),
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'programId' => (int) $validated['programId'],
            'blocked' => false,
        ], 'Program unlocked');
    }

    #[OA\Post(
        path: "/v1/payments/split",
        summary: "Перенести студента в другую группу",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "fromGroup", "toGroup", "effectiveDate"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "fromGroup", type: "integer", example: 5),
                    new OA\Property(property: "toGroup", type: "integer", example: 8),
                    new OA\Property(property: "effectiveDate", type: "string", format: "date", example: "2024-02-01")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Студент перенесен в другую группу"
            ),
            new OA\Response(
                response: 422,
                description: "Ошибка валидации"
            )
        ]
    )]
    public function split(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'fromGroup' => ['required', 'integer'],
            'toGroup' => ['required', 'integer'],
            'effectiveDate' => ['required', 'date'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->group_id = $validated['toGroup'];
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'split',
            'effective_date' => $validated['effectiveDate'],
            'from_group' => (int) $validated['fromGroup'],
            'to_group' => (int) $validated['toGroup'],
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'programId' => (int) $validated['programId'],
            'fromGroup' => (int) $validated['fromGroup'],
            'toGroup' => (int) $validated['toGroup'],
            'effectiveDate' => $validated['effectiveDate'],
        ], 'Program split applied');
    }

    #[OA\Post(
        path: "/v1/payments/archive",
        summary: "Архивировать программу",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId", "reason", "endDate"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12),
                    new OA\Property(property: "reason", type: "string", maxLength: 255, example: "Завершение обучения"),
                    new OA\Property(property: "endDate", type: "string", format: "date", example: "2024-06-30"),
                    new OA\Property(property: "comment", type: "string", maxLength: 1000, nullable: true, example: "По заявлению")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Программа архивирована"),
            new OA\Response(response: 422, description: "Ошибка валидации")
        ]
    )]
    public function archive(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
            'reason' => ['required', 'string', 'max:255'],
            'endDate' => ['required', 'date'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->deleted = 1;
        $student->enabled = 0;
        $student->blocked = 1;
        $student->date_finish = $validated['endDate'];
        $student->blocking_reason = trim($validated['reason'] . ' ' . ($validated['comment'] ?? ''));
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'archive',
            'effective_date' => $validated['endDate'],
            'reason' => $validated['reason'],
            'comment' => $validated['comment'] ?? null,
            'payload' => $validated,
        ]);

        return $this->successResponse($validated, 'Program archived');
    }

    #[OA\Post(
        path: "/v1/payments/resume",
        summary: "Возобновить программу",
        tags: ["Payments"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["programId"],
                properties: [
                    new OA\Property(property: "programId", type: "integer", example: 12)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Программа возобновлена"),
            new OA\Response(response: 422, description: "Ошибка валидации")
        ]
    )]
    public function resume(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'programId' => ['required', 'integer', 'exists:student,id'],
        ]);

        $student = Student::findOrFail($validated['programId']);
        $student->deleted = 0;
        $student->enabled = 1;
        $student->blocked = 0;
        $student->blocking_reason = null;
        $student->save();

        $this->programEventRepository->create([
            'student_id' => (int) $validated['programId'],
            'event_type' => 'resume',
            'effective_date' => now()->toDateString(),
            'payload' => $validated,
        ]);

        return $this->successResponse([
            'programId' => (int) $validated['programId'],
            'status' => 'active',
        ], 'Program resumed');
    }

    public function getStudentProjects(int $studentId): JsonResponse
    {
        $student = Student::query()->find($studentId);
        if (!$student) {
            return $this->errorResponse('Student not found', 404);
        }

        $programs = $this->buildProgramsForFrontend($student);
        $projects = collect($programs)
            ->filter(static fn (array $p) => $p['id'] !== 'extras')
            ->map(static fn (array $p) => [
                'id' => $p['id'],
                'name' => $p['name'],
                'sub' => $p['sub'],
                'tariff' => $p['tariff'],
                'balance' => $p['balance'],
                'balanceLabel' => $p['balanceLabel'],
                'barGradient' => $p['barGradient'],
            ])
            ->values();

        return $this->successResponse([
            'items' => $projects,
        ]);
    }

    public function getStudentProjectCalendar(Request $request, int $studentId, string $projectId): JsonResponse
    {
        $student = Student::query()->find($studentId);
        if (!$student) {
            return $this->errorResponse('Student not found', 404);
        }

        if ($projectId === 'extras') {
            return $this->successResponse([
                'projectId' => 'extras',
                'years' => (object) [],
                'extras' => $this->buildExtrasForStudent($studentId, null),
            ]);
        }

        $resolved = $this->resolveProjectByFrontendId($projectId);
        if (!$resolved) {
            return $this->errorResponse('Project not found', 404);
        }

        $project = $resolved['project'];
        $frontendProjectId = $resolved['frontendId'];

        $year = $request->query('year');

        $chargesQuery = GlsPaymentCharge::query()
            ->where('student_id', $studentId)
            ->where('project_id', $projectId)
            ->orderBy('period_year')
            ->orderBy('period_month');

        if ($year !== null && $year !== '') {
            $chargesQuery->where('period_year', (int) $year);
        }

        $charges = $chargesQuery->get();

        $years = [];
        foreach ($charges as $charge) {
            if (!$charge->period_year || !$charge->period_month) {
                continue;
            }

            if (!isset($years[$charge->period_year])) {
                $years[$charge->period_year] = array_fill(0, 12, [
                    's' => 'future',
                    'a' => 0,
                    'ksef' => null,
                    'g1' => 0,
                    'g2' => 0,
                ]);
            }

            $monthIndex = max(1, min(12, (int) $charge->period_month)) - 1;
            $ksef = $this->resolveKsefStatusForCharge((int) $charge->id);
            $years[$charge->period_year][$monthIndex] = [
                's' => $this->mapChargeStatusToMonthStatus((string) $charge->status),
                'a' => (float) $charge->final_amount,
                'ksef' => $ksef,
                'g1' => 4,
                'g2' => 0,
                'txDate' => $charge->charge_date ? $charge->charge_date->format('d.m.Y') : null,
            ];
        }

        return $this->successResponse([
            'projectId' => $frontendProjectId,
            'years' => $years,
            'extras' => $this->buildExtrasForStudent($studentId, (int) $project->id),
        ]);
    }

    public function getStudentProjectTransactions(Request $request, int $studentId, string $projectId): JsonResponse
    {
        $student = Student::query()->find($studentId);
        if (!$student) {
            return $this->errorResponse('Student not found', 404);
        }

        if ($projectId === 'extras') {
            return $this->successResponse([
                'projectId' => 'extras',
                'items' => $this->buildExtraTransactions($studentId),
            ]);
        }

        $resolved = $this->resolveProjectByFrontendId($projectId);
        if (!$resolved) {
            return $this->errorResponse('Project not found', 404);
        }

        $project = $resolved['project'];
        $frontendProjectId = $resolved['frontendId'];

        $status = $request->query('status');

        $query = GlsPaymentTransaction::query()
            ->where('student_id', $studentId)
            ->where('project_id', $projectId)
            ->orderByDesc('id');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $transactions = $query->get()->map(function (GlsPaymentTransaction $transaction) use ($project) {
            $txStatus = in_array((string) $transaction->status, ['paid', 'refunded', 'partially_refunded'], true)
                ? 'paid'
                : 'pending';

            $doc = GlsInvoiceDocument::query()
                ->where('transaction_id', $transaction->id)
                ->orderByDesc('id')
                ->first();

            return [
                'id' => (string) $transaction->id,
                'date' => $transaction->paid_at ? $transaction->paid_at->format('d.m.Y') : $transaction->created_at?->format('d.m.Y'),
                'title' => 'Абонемент ' . ($project->name ?? 'программа'),
                'sub' => $project->name . ' · ' . (string) $transaction->provider,
                'amount' => (float) $transaction->amount,
                'amountFmt' => (float) $transaction->amount . ' zł',
                'status' => $txStatus,
                'type' => 'month',
                'ksef' => $this->mapKsefStatus($doc?->ksef_status),
                'fvnum' => $doc?->number,
                'documentId' => $doc?->id,
            ];
        })->values();

        return $this->successResponse([
            'projectId' => $frontendProjectId,
            'items' => $transactions,
        ]);
    }

    private function buildProgramsForFrontend(Student $student): array
    {
        $catalog = [
            'space' => [
                'code' => 'space_memory',
                'name' => '🌌 Space Memory',
                'sub' => 'Основная программа',
                'barGradient' => 'linear-gradient(180deg,var(--blue),var(--purple))',
            ],
            'indigo' => [
                'code' => 'indigo',
                'name' => '⚡ Speedy Mind Indigo',
                'sub' => 'Indigo программа',
                'barGradient' => 'linear-gradient(180deg,var(--purple),var(--pink))',
            ],
        ];

        $programs = [];
        foreach ($catalog as $frontendId => $cfg) {
            $project = GlsProject::query()->where('code', $cfg['code'])->first();
            $projectId = $project?->id;

            $charges = GlsPaymentCharge::query()
                ->where('student_id', $student->id)
                ->when($projectId, static fn ($q) => $q->where('project_id', $projectId))
                ->orderBy('period_year')
                ->orderBy('period_month')
                ->get();

            $years = $this->buildYearsFromCharges($charges);

            $tariff = (float) ($charges->sortByDesc('id')->first()->final_amount ?? ($student->sum_aboniment ?? 0));
            $chargesTotal = (float) $charges->sum('final_amount');

            $paidTotal = (float) GlsPaymentTransaction::query()
                ->where('student_id', $student->id)
                ->when($projectId, static fn ($q) => $q->where('project_id', $projectId))
                ->whereIn('status', ['paid', 'partially_refunded', 'refunded'])
                ->sum('amount');

            $balance = round($paidTotal - $chargesTotal, 2);

            $programs[] = [
                'id' => $frontendId,
                'name' => $cfg['name'],
                'sub' => $cfg['sub'],
                'tariff' => $tariff,
                'balance' => $balance,
                'balanceLabel' => $balance >= 0 ? 'переплата' : 'к оплате',
                'barGradient' => $cfg['barGradient'],
                'years' => $years,
                'transactions' => [],
                'extras' => [],
            ];
        }

        $extras = $this->buildExtrasForStudent((int) $student->id, null);
        $programs[] = [
            'id' => 'extras',
            'name' => '📚 Доп. материалы и программы',
            'sub' => 'Разовые услуги и товары',
            'tariff' => 0,
            'balance' => (float) array_sum(array_map(static fn (array $i) => (float) ($i['status'] === 'paid' ? $i['price'] : 0), $extras)),
            'balanceLabel' => 'оплачено',
            'barGradient' => 'linear-gradient(180deg,var(--amber),var(--orange))',
            'years' => (object) [],
            'transactions' => [],
            'extras' => $extras,
        ];

        return $programs;
    }

    private function buildYearsFromCharges($charges): array
    {
        $years = [];
        foreach ($charges as $charge) {
            if (!$charge->period_year || !$charge->period_month) {
                continue;
            }

            $year = (string) $charge->period_year;
            if (!isset($years[$year])) {
                $years[$year] = array_fill(0, 12, [
                    's' => 'future',
                    'a' => 0,
                    'ksef' => null,
                    'g1' => 0,
                    'g2' => 0,
                ]);
            }

            $monthIndex = max(1, min(12, (int) $charge->period_month)) - 1;
            $years[$year][$monthIndex] = [
                's' => $this->mapChargeStatusToMonthStatus((string) $charge->status),
                'a' => (float) $charge->final_amount,
                'ksef' => $this->resolveKsefStatusForCharge((int) $charge->id),
                'g1' => 4,
                'g2' => 0,
                'txDate' => $charge->charge_date ? $charge->charge_date->format('d.m.Y') : null,
            ];
        }

        return $years;
    }

    private function mapChargeStatusToMonthStatus(string $status): string
    {
        return match ($status) {
            'paid', 'closed', 'overpayment' => 'paid',
            'partially_paid' => 'partial',
            'overdue' => 'overdue',
            'paused' => 'pause',
            'pending', 'draft' => 'pending',
            default => 'future',
        };
    }

    private function resolveKsefStatusForCharge(int $chargeId): ?string
    {
        $ksef = GlsInvoiceDocument::query()->where('charge_id', $chargeId)->orderByDesc('id')->value('ksef_status');
        return $this->mapKsefStatus($ksef);
    }

    private function mapKsefStatus(?string $ksef): ?string
    {
        return match ($ksef) {
            'accepted', 'sent', 'not_required' => 'ok',
            'pending', 'draft' => 'pending',
            'error' => 'error',
            'conflict' => 'conflict',
            default => null,
        };
    }

    private function resolveProjectByFrontendId(string $projectId): ?array
    {
        $frontendToCode = [
            'space' => 'space_memory',
            'indigo' => 'indigo',
        ];

        if (isset($frontendToCode[$projectId])) {
            $project = GlsProject::query()->where('code', $frontendToCode[$projectId])->first();
            return $project ? ['frontendId' => $projectId, 'project' => $project] : null;
        }

        if (ctype_digit($projectId)) {
            $project = GlsProject::query()->find((int) $projectId);
            if (!$project) {
                return null;
            }

            $frontendId = $project->code === 'space_memory' ? 'space' : ($project->code === 'indigo' ? 'indigo' : (string) $project->id);
            return ['frontendId' => $frontendId, 'project' => $project];
        }

        return null;
    }

    private function buildExtrasForStudent(int $studentId, ?int $projectId): array
    {
        $query = GlsLessonAdditional::query()->where('student_id', $studentId)->orderByDesc('lesson_date')->orderByDesc('id');
        if ($projectId !== null) {
            $query->where('project_id', $projectId);
        }

        return $query->limit(50)->get()->map(function (GlsLessonAdditional $extra) {
            $isPaid = in_array((string) $extra->status, ['paid', 'approved'], true);

            $title = match ((string) $extra->additional_type) {
                'extra_lesson' => 'Доп. занятие',
                'bonus_class' => 'Бонусный урок',
                'makeup_class' => 'Отработка занятия',
                default => 'Доп. услуга',
            };

            return [
                'id' => 'ext_' . $extra->id,
                'icon' => $extra->additional_type === 'bonus_class' ? '🏆' : ($extra->additional_type === 'makeup_class' ? '🧮' : '👩‍💼'),
                'title' => $extra->comment ?: $title,
                'price' => (float) $extra->final_amount,
                'date' => $isPaid && $extra->lesson_date ? $extra->lesson_date->format('d.m.Y') : null,
                'txId' => $isPaid ? ('#EXTRA-' . str_pad((string) $extra->id, 6, '0', STR_PAD_LEFT)) : null,
                'ksef' => $isPaid ? 'ok' : 'pending',
                'status' => $isPaid ? 'paid' : 'pending',
            ];
        })->values()->all();
    }

    private function buildExtraTransactions(int $studentId): array
    {
        return collect($this->buildExtrasForStudent($studentId, null))
            ->map(static function (array $item) {
                return [
                    'id' => $item['id'],
                    'date' => $item['date'] ?? now()->format('d.m.Y'),
                    'title' => $item['title'],
                    'sub' => 'Доп. материалы и программы',
                    'amount' => (float) $item['price'],
                    'amountFmt' => (float) $item['price'] . ' zł',
                    'status' => $item['status'],
                    'type' => 'extra',
                    'ksef' => $item['ksef'] === 'ok' ? 'ok' : 'pending',
                    'fvnum' => $item['txId'],
                    'documentId' => str_starts_with((string)$item['id'], 'ext_') ? (int)str_replace('ext_', '', $item['id']) : null, // Fallback for extra
                ];
            })
            ->values()
            ->all();
    }

    private function successResponse(array $data, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}


