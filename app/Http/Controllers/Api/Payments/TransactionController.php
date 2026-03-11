<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payments\CreateTransactionRequest;
use App\Http\Requests\Api\Payments\GetStudentTransactionsRequest;
use App\Http\Requests\Api\Payments\UpdateTransactionStatusRequest;
use App\Models\GlsPaymentTransaction;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function index(GetStudentTransactionsRequest $request, int $id): JsonResponse
    {
        if (!Student::query()->where('id', $id)->exists()) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validated = $request->validated();
        $sortBy = $validated['sort_by'] ?? 'id';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = GlsPaymentTransaction::query()
            ->where('student_id', $id)
            ->where('project_id', $validated['project_id'])
            ->orderBy($sortBy, $sortDir);

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $paginator = $query->paginate($perPage)->appends($request->query());

        $transactions = collect($paginator->items())->map(static function (GlsPaymentTransaction $item) {
            return [
                'id' => (int) $item->id,
                'provider' => $item->provider,
                'direction' => $item->direction,
                'amount' => number_format((float) $item->amount, 2, '.', ''),
                'currency' => $item->currency,
                'status' => $item->status,
                'paid_at' => $item->paid_at ? $item->paid_at->format('Y-m-d H:i:s') : null,
            ];
        })->values();

        return response()->json([
            'student_id' => $id,
            'project_id' => (int) $validated['project_id'],
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(CreateTransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $transaction = GlsPaymentTransaction::query()->create([
            'student_id' => $validated['student_id'],
            'project_id' => $validated['project_id'],
            'provider' => $validated['provider'],
            'direction' => $validated['direction'],
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency'] ?? 'PLN'),
            'status' => $validated['status'],
            'external_id' => $validated['external_id'] ?? null,
            'paid_at' => $validated['paid_at'] ?? null,
            'provider_payload' => $validated['provider_payload'] ?? null,
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'transaction_id' => (int) $transaction->id,
            'status' => $transaction->status,
        ], 201);
    }

    public function updateStatus(UpdateTransactionStatusRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $transaction = GlsPaymentTransaction::query()
            ->where('id', $id)
            ->where('project_id', $validated['project_id'])
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transaction->status = $validated['status'];
        $transaction->save();

        return response()->json([
            'transaction_id' => (int) $transaction->id,
            'status' => $transaction->status,
        ]);
    }
}

