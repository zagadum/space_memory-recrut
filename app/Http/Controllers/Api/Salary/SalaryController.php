<?php

namespace App\Http\Controllers\Api\Salary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Salary\ConfirmSalaryRequest;
use App\Http\Requests\Api\Salary\DisputeSalaryRequest;
use App\Http\Requests\Api\Salary\GetTeacherSalaryRequest;
use App\Models\GlsSalaryCalculation;
use App\Models\GlsSalaryDispute;
use Illuminate\Http\JsonResponse;

class SalaryController extends Controller
{
    public function showTeacherSalary(GetTeacherSalaryRequest $request, int $teacherId): JsonResponse
    {
        $validated = $request->validated();
        $projectId = (int) ($validated['project_id'] ?? 1);

        $periodYear = null;
        $periodMonth = null;

        if (!empty($validated['month'])) {
            [$periodYear, $periodMonth] = array_map('intval', explode('-', $validated['month']));
        }

        if (!empty($validated['year'])) {
            $periodYear = (int) $validated['year'];
        }

        if (!empty($validated['period_month'])) {
            $periodMonth = (int) $validated['period_month'];
        }

        $query = GlsSalaryCalculation::query()
            ->where('project_id', $projectId)
            ->where('teacher_id', $teacherId);

        if ($periodYear !== null) {
            $query->where('period_year', $periodYear);
        }
        if ($periodMonth !== null) {
            $query->where('period_month', $periodMonth);
        }

        $calculation = $query
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->first();

        if (!$calculation) {
            return response()->json(['message' => 'Salary calculation not found'], 404);
        }

        $payload = $calculation->payload ?? [];

        $response = array_merge($payload, [
            'id' => (string) $calculation->id,
            'month' => sprintf('%04d-%02d', $calculation->period_year, $calculation->period_month),
            'status' => $calculation->status,
            'confirmedAt' => $calculation->confirmed_at ? $calculation->confirmed_at->format('Y-m-d H:i:s') : null,
            'projectId' => (int) $calculation->project_id,
            'teacherId' => (int) $calculation->teacher_id,
        ]);

        return response()->json($response);
    }

    public function confirm(ConfirmSalaryRequest $request, int $id): JsonResponse
    {
        $projectId = (int) ($request->validated()['project_id'] ?? 1);

        $calculation = GlsSalaryCalculation::query()
            ->where('id', $id)
            ->where('project_id', $projectId)
            ->first();

        if (!$calculation) {
            return response()->json(['message' => 'Salary calculation not found'], 404);
        }

        $calculation->status = 'confirmed';
        $calculation->confirmed_at = now();
        $calculation->save();

        return response()->json([
            'id' => (int) $calculation->id,
            'status' => $calculation->status,
            'confirmedAt' => $calculation->confirmed_at ? $calculation->confirmed_at->format('Y-m-d H:i:s') : null,
        ]);
    }

    public function dispute(DisputeSalaryRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $projectId = (int) ($validated['project_id'] ?? 1);

        $calculation = GlsSalaryCalculation::query()
            ->where('id', $id)
            ->where('project_id', $projectId)
            ->first();

        if (!$calculation) {
            return response()->json(['message' => 'Salary calculation not found'], 404);
        }

        $dispute = GlsSalaryDispute::query()->create([
            'salary_calculation_id' => $calculation->id,
            'teacher_id' => (int) $validated['teacher_id'],
            'reason' => $validated['reason'],
            'status' => 'open',
        ]);

        $calculation->status = 'disputed';
        $calculation->save();

        return response()->json([
            'id' => (int) $dispute->id,
            'salary_calculation_id' => (int) $calculation->id,
            'status' => $calculation->status,
        ], 201);
    }
}

