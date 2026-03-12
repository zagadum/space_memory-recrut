<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Events\StudentCreatedEvent;
use App\Events\StudentUpdatedEvent;
use App\Events\StudentArchivedEvent;
use App\Models\RecrutingStudentHistory;

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
                ->get();

            return response()->json([
                'success' => true,
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'           => 'nullable|string|max:100',
            'surname'        => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:150|unique:recruting_student,email,' . $id,
            'phone'          => 'nullable|string|max:20',
            'subject'        => 'nullable|string|max:100',
            'status'         => 'nullable|in:new,registered,paid,expelled,transferred,archived',
            'group_id'       => 'nullable|integer',
            'teacher_id'     => 'nullable|integer',
            'parent_name'    => 'nullable|string|max:100',
            'parent_surname' => 'nullable|string|max:100',
            'parent_phone'   => 'nullable|string|max:20',
            'city'           => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:200',
            'zip'            => 'nullable|string|max:20',
            'dob'            => 'nullable|date',
        ]);

        $student = DB::table('recruting_student')->where('id', $id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        DB::table('recruting_student')
            ->where('id', $id)
            ->update($validated);

        // Записать в историю что данные были изменены
        event(new StudentUpdatedEvent(
            $id,
            'Данные обновлены менеджером',
            $request->header('X-Manager-Name', 'Admin'),
            ['fields' => array_keys($validated)]
        ));

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'surname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'email' => 'required|email|unique:recruting_student,email',
                'group_id' => 'nullable|integer',
                'teacher_id' => 'nullable|integer',
            ]);

            $now = now();
            $id = DB::table('recruting_student')->insertGetId([
                'name' => $request->name,
                'surname' => $request->surname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'status' => 'new',
                'group_id' => $request->group_id,
                'teacher_id' => $request->teacher_id,
                'password' => Hash::make(Str::random(12)),
                'enabled' => 1,
                'blocked' => 0,
                'deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            $student = DB::table('recruting_student')
                ->where('id', $id)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'group_id', 'teacher_id', 'created_at')
                ->first();

            event(new StudentCreatedEvent(
                studentId: $id,
                detail: 'Создан администратором',
                changedBy: $request->input('manager') ?? 'Admin'
            ));

            return response()->json([
                'success' => true,
                'data' => $student
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function archive($id)
    {
        try {
            $updated = DB::table('recruting_student')
                ->where('id', $id)
                ->update([
                    'deleted' => 1,
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            event(new StudentArchivedEvent(
                studentId: $id,
                detail: 'Студент перемещён в архив',
                changedBy: null
            ));

            return response()->json([
                'success' => true,
                'data' => ['id' => $id]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email|unique:recruting_student,email',
            'password' => 'required|min:6',
        ]);

        $data = [
            'email'            => $request->email,
            'password'         => bcrypt($request->password),
            'status'           => 'registered',
            'name'             => $request->name ?? '',
            'surname'          => $request->surname ?? '',
            'lastname'         => $request->lastname ?? '',
            'parent_name'      => $request->parent_name ?? '',
            'parent_surname'   => $request->parent_surname ?? '',
            'parent_phone'     => $request->parent_phone ?? '',
            'parent_passport'  => $request->parent_passport ?? '',
            'dob'              => $request->dob ?? null,
            'country'          => $request->country ?? '',
            'city'             => $request->city ?? '',
            'address'          => $request->address ?? '',
            'zip'              => $request->zip ?? '',
            'apartment'        => $request->apartment ?? '',
            'photo_consent'    => $request->photo_consent ?? 0,
            'terms_accepted'   => $request->terms_accepted ?? 0,
            'privacy_accepted' => $request->privacy_accepted ?? 0,
            'reg_comment'      => $request->reg_comment ?? '',
            'enabled'          => 0,
            'blocked'          => 0,
            'deleted'          => 0,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];

        $id = DB::table('recruting_student')->insertGetId($data);

        event(new StudentCreatedEvent(
            studentId: $id,
            detail: 'Зарегистрирован через форму',
            changedBy: 'System'
        ));

        return response()->json([
            'success' => true,
            'message' => 'Регистрация успешна'
        ], 201);
    }

    public function history($id)
    {
        $history = RecrutingStudentHistory::where('student_id', $id)
            ->orderBy('created_at', 'desc')
            ->get(['event', 'detail', 'changed_by', 'created_at']);

        return response()->json(['data' => $history]);
    }
}
