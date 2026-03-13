<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStudentRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
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
                    'parent_name', 'parent_surname', 'parent_phone', 'parent_passport',
                    'dob', 'country', 'city', 'address', 'zip', 'apartment',
                    'photo_consent', 'terms_accepted', 'privacy_accepted', 'reg_comment'
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
                'id', 'name', 'surname', 'lastname', 'email',
                'group_id', 'teacher_id', 'created_at',
                'parent_name', 'parent_surname', 'parent_phone', 'parent_passport',
                'dob', 'country', 'city', 'address', 'zip', 'apartment',
                'photo_consent', 'terms_accepted', 'privacy_accepted', 'reg_comment'
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
        $validated = $request->validated();
        $code      = (string) rand(1000, 9999);

        $data = [
            'email'             => $validated['email'],
            'password'          => bcrypt($validated['password']),
            'status'            => 'registered',
            'name'              => $validated['name']             ?? '',
            'surname'           => $validated['surname']          ?? '',
            'lastname'          => $validated['lastname']         ?? '',
            'parent_name'       => $validated['parent_name']      ?? '',
            'parent_surname'    => $validated['parent_surname']   ?? '',
            'parent_phone'      => $validated['parent_phone']     ?? '',
            'parent_passport'   => $validated['parent_passport']  ?? '',
            'dob'               => $validated['dob']              ?? null,
            'country'           => $validated['country']          ?? '',
            'city'              => $validated['city']             ?? '',
            'address'           => $validated['address']          ?? '',
            'zip'               => $validated['zip']              ?? '',
            'apartment'         => $validated['apartment']        ?? '',
            'photo_consent'     => $validated['photo_consent']    ?? 0,
            'terms_accepted'    => $validated['terms_accepted']   ?? 0,
            'privacy_accepted'  => $validated['privacy_accepted'] ?? 0,
            'reg_comment'       => $validated['reg_comment']      ?? '',
            'verification_code' => $code,
            'enabled'           => 0,
            'blocked'           => 0,
            'deleted'           => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
        ];

        try {
            $id = DB::table('recruting_student')->insertGetId($data);

            Log::info("Student registered: {$validated['email']}, ID: {$id}");

            SendVerificationCodeJob::dispatch($validated['email'], $code);

            event(new StudentCreatedEvent(
                studentId: $id,
                detail:    'Зарегистрирован через форму',
                changedBy: 'System'
            ));

            return response()->json(['success' => true, 'message' => 'Регистрация успешна'], 201);

        } catch (\Exception $e) {
            Log::error("Registration error for {$validated['email']}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при регистрации: ' . $e->getMessage(),
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
