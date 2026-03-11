<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payments\CreateAdditionalLessonRequest;
use App\Http\Requests\Api\Payments\GetStudentAdditionalLessonsRequest;
use App\Models\GlsLessonAdditional;
use Illuminate\Http\JsonResponse;

class LessonAdditionalController extends Controller
{
    public function index(GetStudentAdditionalLessonsRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $sortBy = $validated['sort_by'] ?? 'lesson_date';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $perPage = (int) ($validated['per_page'] ?? 20);

        $paginator = GlsLessonAdditional::query()
            ->where('student_id', $id)
            ->where('project_id', $validated['project_id'])
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->appends($request->query());

        $lessons = collect($paginator->items())->map(static function (GlsLessonAdditional $item) {
            return [
                'id' => (int) $item->id,
                'lesson_date' => $item->lesson_date ? $item->lesson_date->format('Y-m-d') : null,
                'additional_type' => $item->additional_type,
                'final_amount' => number_format((float) $item->final_amount, 2, '.', ''),
                'status' => $item->status,
            ];
        })->values();

        return response()->json([
            'student_id' => $id,
            'project_id' => (int) $validated['project_id'],
            'lesson_additional' => $lessons,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(CreateAdditionalLessonRequest $request): JsonResponse
    {
        $lesson = GlsLessonAdditional::query()->create($request->validated());

        return response()->json([
            'lesson_id' => (int) $lesson->id,
            'status' => $lesson->status,
        ], 201);
    }
}

