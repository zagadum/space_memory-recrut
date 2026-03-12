<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpelledStudentsController extends Controller
{
    // Список выписанных — status = 'expelled'
    // Это НЕ архив. Это ученики с которыми ещё работают продажи.
    public function index(Request $request)
    {
        $query = DB::table('recruting_student')
            ->where('status', 'expelled')
            ->where('deleted', 0)
            ->select([
                'id',
                'name',
                'surname',
                'email',
                'phone',
                'subject',
                'group_id',
                'teacher_id',
                'status',
                'updated_at',
                'created_at'
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('updated_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $students
        ]);
    }

    // Обновление — менеджер может изменить данные
    // или перевести обратно в new (передумал) или в archived (окончательно отказ)
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status'     => 'nullable|in:new,expelled,archived',
            'teacher_id' => 'nullable|integer',
            'group_id'   => 'nullable|integer',
            'phone'      => 'nullable|string|max:20',
        ]);

        DB::table('recruting_student')
            ->where('id', $id)
            ->update($validated);

        return response()->json(['success' => true]);
    }

    // Перевести в архив — окончательный отказ
    public function archive(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:3|max:500',
        ]);

        DB::table('recruting_student')
            ->where('id', $id)
            ->update(['status' => 'archived']);

        return response()->json(['success' => true]);
    }
}
