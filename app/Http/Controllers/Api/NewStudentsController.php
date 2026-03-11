<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewStudentsController extends Controller
{
    public function index()
    {
        try {
            $students = DB::table('recruting_student')
                ->where('deleted', 0)
                ->where('enabled', 1)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'group_id', 'teacher_id', 'created_at')
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
}
