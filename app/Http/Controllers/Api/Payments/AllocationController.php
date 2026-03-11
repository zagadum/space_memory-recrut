<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payments\AllocateTransactionRequest;
use App\Http\Requests\Api\Payments\GetTransactionAllocationsRequest;
use App\Models\GlsPaymentAllocation;
use App\Models\GlsPaymentCharge;
use App\Models\GlsPaymentTransaction;
use Illuminate\Http\JsonResponse;

class AllocationController extends Controller
{
    public function index(GetTransactionAllocationsRequest $request, int $id): JsonResponse
    {
        $projectId = (int) $request->validated()['project_id'];

        $transaction = GlsPaymentTransaction::query()
            ->where('id', $id)
            ->where('project_id', $projectId)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $allocations = GlsPaymentAllocation::query()
            ->where('transaction_id', $transaction->id)
            ->orderBy('id')
            ->get(['charge_id', 'amount'])
            ->map(static function (GlsPaymentAllocation $item) {
                return [
                    'charge_id' => (int) $item->charge_id,
                    'amount' => number_format((float) $item->amount, 2, '.', ''),
                ];
            })
            ->values();

        return response()->json([
            'transaction_id' => (int) $transaction->id,
            'allocations' => $allocations,
        ]);
    }

    public function store(AllocateTransactionRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $transaction = GlsPaymentTransaction::query()
            ->where('id', $id)
            ->where('project_id', $validated['project_id'])
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $charge = GlsPaymentCharge::query()
            ->where('id', $validated['charge_id'])
            ->where('project_id', $validated['project_id'])
            ->first();

        if (!$charge) {
            return response()->json(['message' => 'Charge not found for project'], 422);
        }

        $transactionAllocated = (float) GlsPaymentAllocation::query()
            ->where('transaction_id', $transaction->id)
            ->sum('amount');

        if ($transactionAllocated + (float) $validated['amount'] > (float) $transaction->amount) {
            return response()->json(['message' => 'Allocation exceeds transaction amount'], 422);
        }

        $chargeAllocated = (float) GlsPaymentAllocation::query()
            ->where('charge_id', $charge->id)
            ->sum('amount');

        if ($chargeAllocated + (float) $validated['amount'] > (float) $charge->final_amount) {
            return response()->json(['message' => 'Allocation exceeds charge amount'], 422);
        }

        $allocation = GlsPaymentAllocation::query()->create([
            'transaction_id' => $transaction->id,
            'charge_id' => $charge->id,
            'amount' => $validated['amount'],
            'allocated_at' => now(),
            'created_at' => now(),
        ]);

        return response()->json([
            'allocation_id' => (int) $allocation->id,
        ], 201);
    }
}

