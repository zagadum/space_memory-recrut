<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LeadsController extends Controller
{
    public function index()
    {
        try {
            $leads = DB::table('recruting_student')
                ->where('deleted', 0)
                ->whereNotIn('status', ['archived', 'transferred'])
                ->select('id', 'name', 'surname', 'lastname', 'email', 'status', 'phone', 'subject', 'created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $leads
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
                'phone' => 'nullable|string|max:20',
                'subject' => 'nullable|string|max:100',
            ]);

            $now = now();
            $id = DB::table('recruting_student')->insertGetId([
                'name' => $request->name,
                'surname' => $request->surname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'status' => 'new',
                'phone' => $request->phone ?? null,
                'subject' => $request->subject ?? null,
                'password' => Hash::make(Str::random(12)),
                'enabled' => 0,
                'blocked' => 0,
                'deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $lead = DB::table('recruting_student')
                ->where('id', $id)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'status', 'phone', 'subject', 'created_at')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $lead
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

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'nullable|string|max:255',
                'surname' => 'nullable|string|max:255',
                'lastname' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:recruting_student,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'subject' => 'nullable|string|max:100',
                'status' => 'nullable|in:new,registered,paid,expelled,transferred,archived',
                'group_id' => 'nullable|integer',
                'teacher_id' => 'nullable|integer',
                'enabled' => 'nullable|boolean',
            ]);

            $updateData = $request->only(['name', 'surname', 'lastname', 'email', 'phone', 'subject', 'status', 'group_id', 'teacher_id']);

            if ($request->has('enabled')) {
                $updateData['enabled'] = $request->enabled ? 1 : 0;
            }

            if (!empty($updateData)) {
                $updateData['updated_at'] = now();

                $updated = DB::table('recruting_student')
                    ->where('id', $id)
                    ->update($updateData);

                if (!$updated && empty($updateData)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lead not found or no changes provided'
                    ], 404);
                }
            }

            $lead = DB::table('recruting_student')
                ->where('id', $id)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'status', 'phone', 'subject', 'group_id', 'teacher_id', 'enabled', 'created_at')
                ->first();

            if (!$lead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $lead
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
}
