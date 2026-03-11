<?php

namespace App\Http\Controllers\Api\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Payments\CreateDocumentRequest;
use App\Http\Requests\Api\Payments\GetStudentDocumentsRequest;
use App\Http\Requests\Api\Payments\UpdateDocumentKsefStatusRequest;
use App\Models\GlsInvoiceDocument;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function index(GetStudentDocumentsRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $sortBy = $validated['sort_by'] ?? 'issue_date';
        $sortDir = $validated['sort_dir'] ?? 'desc';
        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = GlsInvoiceDocument::query()
            ->where('student_id', $id)
            ->where('project_id', $validated['project_id'])
            ->orderBy($sortBy, $sortDir);

        if (!empty($validated['document_type'])) {
            $query->where('document_type', $validated['document_type']);
        }

        if (!empty($validated['ksef_status'])) {
            $query->where('ksef_status', $validated['ksef_status']);
        }

        $paginator = $query->paginate($perPage)->appends($request->query());

        $documents = collect($paginator->items())->map(static function (GlsInvoiceDocument $item) {
            return [
                'id' => (int) $item->id,
                'type' => $item->document_type,
                'amount' => number_format((float) $item->amount_gross, 2, '.', ''),
                'number' => $item->number,
                'issue_date' => $item->issue_date ? $item->issue_date->format('Y-m-d') : null,
                'ksef_status' => $item->ksef_status,
            ];
        })->values();

        return response()->json([
            'student_id' => $id,
            'project_id' => (int) $validated['project_id'],
            'documents' => $documents,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(CreateDocumentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $document = GlsInvoiceDocument::query()->create([
            ...$validated,
            'currency' => strtoupper($validated['currency'] ?? 'PLN'),
        ]);

        return response()->json([
            'document_id' => (int) $document->id,
            'ksef_status' => $document->ksef_status,
        ], 201);
    }

    public function updateKsefStatus(UpdateDocumentKsefStatusRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $document = GlsInvoiceDocument::query()
            ->where('id', $id)
            ->where('project_id', $validated['project_id'])
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $document->ksef_status = $validated['ksef_status'];
        $document->ksef_reference = $validated['ksef_reference'] ?? $document->ksef_reference;
        $document->save();

        return response()->json([
            'document_id' => (int) $document->id,
            'ksef_status' => $document->ksef_status,
            'ksef_reference' => $document->ksef_reference,
        ]);
    }
}

