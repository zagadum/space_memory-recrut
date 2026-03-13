<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Events\StudentCreatedEvent;
use App\Events\StudentUpdatedEvent;
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
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data'    => $leads,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreLeadRequest $request)
    {
        try {
            $validated = $request->validated();
            $now       = now();

            $id = DB::table('recruting_student')->insertGetId([
                'name'       => $validated['name']     ?? null,
                'surname'    => $validated['surname']   ?? null,
                'lastname'   => $validated['lastname']  ?? null,
                'email'      => $validated['email'],
                'status'     => 'new',
                'phone'      => $validated['phone']    ?? null,
                'subject'    => $validated['subject']  ?? null,
                'password'   => Hash::make(Str::random(12)),
                'enabled'    => 0,
                'blocked'    => 0,
                'deleted'    => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $lead = DB::table('recruting_student')
                ->where('id', $id)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'status', 'phone', 'subject', 'created_at')
                ->first();

            event(new StudentCreatedEvent(
                studentId: $id,
                detail:    'Лид создан администратором',
                changedBy: 'Admin'
            ));

            return response()->json(['success' => true, 'data' => $lead], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateLeadRequest $request, $id)
    {
        try {
            $validated = $request->validated();

            $updateData = array_filter(
                $validated,
                fn($value) => $value !== null
            );

            if (!empty($updateData)) {
                $updateData['updated_at'] = now();

                DB::table('recruting_student')
                    ->where('id', $id)
                    ->update($updateData);

                event(new StudentUpdatedEvent(
                    $id,
                    'Данные лида обновлены',
                    'Admin',
                    ['fields' => array_keys($updateData)]
                ));
            }

            $lead = DB::table('recruting_student')
                ->where('id', $id)
                ->select('id', 'name', 'surname', 'lastname', 'email', 'status', 'phone', 'subject', 'group_id', 'teacher_id', 'enabled', 'created_at')
                ->first();

            if (!$lead) {
                return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $lead]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
