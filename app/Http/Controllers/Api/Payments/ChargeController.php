<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payments\CreateChargeRequest;
use App\Http\Requests\Api\Payments\GetStudentChargesRequest;
use App\Http\Requests\Api\Payments\UpdateChargeStatusRequest;
use App\Models\GlsPaymentCharge;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class ChargeController extends Controller
{
    public function index(GetStudentChargesRequest $request, int $id): JsonResponse
    {
        if (!Student::query()->where('id', $id)->exists()) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validated = $request->validated();
        $sortBy = $validated['sort_by'] ?? 'period_year';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = GlsPaymentCharge::query()
            ->where('student_id', $id)
            ->where('project_id', $validated['project_id']);

        if (!empty($validated['year'])) {
            $query->where('period_year', $validated['year']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if ($sortBy === 'period_year') {
            $query->orderBy('period_year', $sortDir)->orderBy('period_month', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $paginator = $query->paginate($perPage)->appends($request->query());

        $charges = collect($paginator->items())->map(static function (GlsPaymentCharge $item) {
            return [
                'id' => (int) $item->id,
                'charge_type' => $item->charge_type,
                'period_year' => $item->period_year,
                'period_month' => $item->period_month,
                'base_amount' => number_format((float) $item->base_amount, 2, '.', ''),
                'discount_amount' => number_format((float) $item->discount_amount, 2, '.', ''),
                'final_amount' => number_format((float) $item->final_amount, 2, '.', ''),
                'status' => $item->status,
                'due_date' => $item->due_date ? $item->due_date->format('Y-m-d') : null,
            ];
        })->values();

        return response()->json([
            'student_id' => $id,
            'project_id' => (int) $validated['project_id'],
            'charges' => $charges,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(CreateChargeRequest $request): JsonResponse
    {
        $charge = GlsPaymentCharge::query()->create($request->validated());

        return response()->json([
            'charge_id' => (int) $charge->id,
            'status' => $charge->status,
        ], 201);
    }

    public function updateStatus(UpdateChargeStatusRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $charge = GlsPaymentCharge::query()
            ->where('id', $id)
            ->where('project_id', $validated['project_id'])
            ->first();

        if (!$charge) {
            return response()->json(['message' => 'Charge not found'], 404);
        }

        $charge->status = $validated['status'];
        $charge->save();

        return response()->json([
            'charge_id' => (int) $charge->id,
            'status' => $charge->status,
        ]);
    }
}

