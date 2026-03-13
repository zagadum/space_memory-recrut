<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateExpelledStudentRequest;
use App\Http\Requests\ArchiveExpelledStudentRequest;
use Illuminate\Support\Facades\DB;

class ExpelledStudentsController extends Controller
{
    // Список выписанных — status = 'expelled'
    // Это НЕ архив. Это ученики, с которыми ещё работают продажи.
    public function index(UpdateExpelledStudentRequest $request)
    {
        $query = DB::table('recruting_student')
            ->where('status', 'expelled')
            ->where('deleted', 0)
            ->select([
                'id', 'name', 'surname', 'email', 'phone',
                'subject', 'group_id', 'teacher_id',
                'status', 'updated_at', 'created_at',
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name',    'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email',   'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('updated_at', 'desc')->paginate(20);

        return response()->json(['success' => true, 'data' => $students]);
    }

    // Обновление — менеджер меняет данные или переводит в new / archived
    public function update(UpdateExpelledStudentRequest $request, $id)
    {
        $validated = $request->validated();

        DB::table('recruting_student')
            ->where('id', $id)
            ->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json(['success' => true]);
    }

    // Перевести в архив — окончательный отказ
    public function archive(ArchiveExpelledStudentRequest $request, $id)
    {
        DB::table('recruting_student')
            ->where('id', $id)
            ->update([
                'status'     => 'archived',
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}
