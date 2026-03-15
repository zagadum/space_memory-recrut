<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStudentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\GlsDocument;
use App\Models\GlsProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Events\StudentCreatedEvent;
use App\Events\StudentUpdatedEvent;
use App\Events\StudentArchivedEvent;
use App\Models\RecrutingStudentHistory;
use App\Jobs\SendVerificationCodeJob;

class NewStudentsController extends Controller
{
    public function index()
    {
        try {
            $students = DB::table('recruting_student')
                ->where('deleted', 0)
                ->whereNotIn('status', ['archived', 'transferred'])
                ->select(
                    'id', 'name', 'surname', 'lastname', 'email', 'status',
                    'group_id', 'teacher_id', 'created_at',
                    // parent1_* — основные поля (формат student.sql)
                    'parent1_surname', 'parent1_lastname', 'parent1_phone', 'parent1_phone_country',
                    // parent_* — расширения (обратная совместимость)
                    'parent_name', 'parent_surname', 'parent_phone', 'parent_passport',
                    'dob', 'country', 'city', 'address', 'zip', 'apartment',
                    'photo_consent', 'terms_accepted', 'privacy_accepted', 'reg_comment',
                    'data_processing_accepted', 'urgent_start_accepted',
                    'recording_consent_accepted', 'marketing_consent_accepted'
                )
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data'    => $students,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $student = DB::table('recruting_student')
            ->where('id', $id)
            ->where('deleted', 0)
            ->select(
                'id', 'name', 'surname', 'lastname', 'email', 'status',
                'group_id', 'teacher_id', 'created_at',
                // parent1_* — основные поля (формат student.sql)
                'parent1_surname', 'parent1_lastname', 'parent1_phone', 'parent1_phone_country',
                // parent_* — расширения (обратная совместимость)
                'parent_name', 'parent_surname', 'parent_phone', 'parent_passport',
                'dob', 'country', 'city', 'address', 'zip', 'apartment',
                'photo_consent', 'terms_accepted', 'privacy_accepted', 'reg_comment',
                'data_processing_accepted', 'urgent_start_accepted',
                'recording_consent_accepted', 'marketing_consent_accepted'
            )
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $student]);
    }

    public function update(UpdateStudentRequest $request, $id)
    {
        $validated = $request->validated();

        $student = DB::table('recruting_student')->where('id', $id)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        DB::table('recruting_student')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        event(new StudentUpdatedEvent(
            $id,
            'Данные обновлены менеджером',
            $request->header('X-Manager-Name', 'Admin'),
            ['fields' => array_keys($validated)]
        ));

        return response()->json(['success' => true]);
    }

    public function store(StoreStudentRequest $request)
    {
        try {
            $validated = $request->validated();
            $now       = now();

            $id = DB::table('recruting_student')->insertGetId([
                'name'       => $validated['name']       ?? null,
                'surname'    => $validated['surname']    ?? null,
                'lastname'   => $validated['lastname']   ?? null,
                'email'      => $validated['email'],
                'status'     => 'new',
                'group_id'   => $validated['group_id']   ?? null,
                'teacher_id' => $validated['teacher_id'] ?? null,
                'password'   => Hash::make(Str::random(12)),
                'enabled'    => 1,
                'blocked'    => 0,
                'deleted'    => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $student = DB::table('recruting_student')
                ->where('id', $id)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'group_id', 'teacher_id', 'created_at')
                ->first();

            event(new StudentCreatedEvent(
                studentId: $id,
                detail:    'Создан администратором',
                changedBy: $validated['manager'] ?? 'Admin'
            ));

            return response()->json(['success' => true, 'data' => $student], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function archive($id)
    {
        try {
            $updated = DB::table('recruting_student')
                ->where('id', $id)
                ->update([
                    'deleted'    => 1,
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json(['success' => false, 'message' => 'Student not found'], 404);
            }

            event(new StudentArchivedEvent(
                studentId: $id,
                detail:    'Студент перемещён в архив',
                changedBy: null
            ));

            return response()->json(['success' => true, 'data' => ['id' => $id]]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function register(RegisterStudentRequest $request)
    {
        try {
            $validated = $request->validated();
            $code      = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $data = [
                // Аккаунт
                'email'    => $validated['email'],
                'password' => bcrypt($validated['password']),
                'status'   => 'registered',
                'enabled'  => 0,
                'deleted'  => 0,
                'blocked'  => 0,

                // Ребёнок (поля как в student.sql)
                // ВНИМАНИЕ: lastname = имя, surname = фамилия (legacy!)
                'surname'  => $validated['surname'] ?? '',
                'lastname' => $validated['name'] ?? '',
                'name'     => $validated['name'] ?? '',
                'dob'      => $validated['dob'] ?? null,
                'language' => $validated['language'] ?? 'pl',

                // ⚠️ КРИТИЧНО: маппинг form parent → parent1_* (формат student.sql)
                'parent1_surname'       => $validated['parent_surname'] ?? '',   // Фамилия родителя
                'parent1_lastname'      => $validated['parent_name'] ?? '',      // Имя родителя (legacy naming!)
                'parent1_phone'         => $validated['parent_phone'] ?? '',
                'parent1_phone_country' => $validated['country'] ?? 'PL',

                // Дубль в расширения recruting_student (обратная совместимость)
                'parent_name'    => $validated['parent_name'] ?? '',
                'parent_surname' => $validated['parent_surname'] ?? '',
                'parent_phone'   => $validated['parent_phone'] ?? '',

                // Адрес (расширения recruting_student)
                'country'   => $validated['country'] ?? '',
                'city'      => $validated['city'] ?? '',
                'address'   => $validated['address'] ?? '',
                'zip'       => $validated['zip'] ?? '',
                'apartment' => $validated['apartment'] ?? '',

                // Согласия
                'photo_consent'               => $validated['photo_consent'] ?? 0,
                'terms_accepted'              => $validated['terms_accepted'] ?? 0,
                'privacy_accepted'            => $validated['privacy_accepted'] ?? 0,
                'data_processing_accepted'    => $validated['data_processing'] ?? 0,
                'urgent_start_accepted'       => $validated['urgent_start'] ?? 0,
                'recording_consent_accepted'  => $validated['recording_consent'] ?? 0,
                'marketing_consent_accepted'  => $validated['marketing_consent'] ?? 0,

                // Комментарий
                'reg_comment' => $validated['reg_comment'] ?? '',

                // Верификация
                'verification_code' => $code,

                // Timestamps
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $studentId = DB::transaction(function () use ($data) {
                $createdStudentId = DB::table('recruting_student')->insertGetId($data);

                $project = GlsProject::query()
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->first();

                if (!$project) {
                    $project = GlsProject::query()->firstOrCreate(
                        ['code' => 'space_memory'],
                        ['name' => 'Space Memory', 'is_active' => true]
                    );
                }

                GlsDocument::query()->create([
                    'student_id' => $createdStudentId,
                    'project_id' => $project->id,
                    'title' => 'Regulamin swiadczenia uslug',
                    'doc_status' => 'new',
                ]);

                return $createdStudentId;
            });

            SendVerificationCodeJob::dispatch($validated['email'], $code);

            event(new StudentCreatedEvent(
                studentId: $studentId,
                detail: 'Регистрация через форму',
                changedBy: 'Registration Form'
            ));

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $studentId,
                    'email' => $validated['email'],
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation error for registration: ", $e->errors());
            return response()->json([
                'success' => false,
                'message' => __('api.validation_error'),
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("Registration error for {$request->email}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => __('Registration error: :message', ['message' => $e->getMessage()]),
            ], 500);
        }
    }

    public function history($id)
    {
        $history = RecrutingStudentHistory::where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get(['event', 'detail', 'changed_by', 'created_at']);

        return response()->json(['data' => $history]);
    }
}
