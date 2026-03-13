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

    /**
     * Download invoice PDF for admin panel.
     * GET /api/v1/payments/documents/{id}/pdf
     */
    public function downloadPdf(int $id): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        $document = GlsInvoiceDocument::query()->findOrFail($id);

        // Case 1: Pre-generated PDF exists
        if ($document->pdf_path && \Storage::disk('private')->exists($document->pdf_path)) {
            $filename = 'Faktura-' . str_replace('/', '-', $document->number ?? $id) . '.pdf';
            return \Storage::disk('private')->download($document->pdf_path, $filename);
        }

        // Case 2: Generate on-the-fly
        $student = $document->student;

        $lineItem = new \App\Services\Invoice\InvoiceLineItem(
            lp:         1,
            nazwa:      $document->title ?? 'Usługa edukacyjna',
            quantity:   1,
            unit:       'szt.',
            unitPrice:  (float) $document->amount_gross,
            vatRate:    'zw.',
            vatAmount:  0.0,
            totalNet:   (float) ($document->amount_net ?: $document->amount_gross),
            totalGross: (float) $document->amount_gross,
        );

        $buyerName = '';
        $buyerAddress = '';
        if ($student) {
            $buyerName = trim(($student->parent1_surname ?? $student->surname ?? '') . ' ' . ($student->parent1_lastname ?? $student->lastname ?? ''));
            $buyerAddress = trim(($student->address ?? '') . ', ' . ($student->zip ?? '') . ' ' . ($student->city ?? ''));
        }

        $invoiceData = new \App\Services\Invoice\InvoiceData(
            documentNumber: $document->number ?? 'DRAFT',
            issueDate:      $document->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            saleDate:       $document->service_date_from?->format('Y-m-d') ?? $document->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            buyerName:      $buyerName,
            buyerAddress:   $buyerAddress,
            buyerNip:       $student->nip ?? null,
            items:          [$lineItem],
            currency:       $document->currency ?? 'PLN',
            bankAccount:    'PL 00 0000 0000 0000 0000 0000 0000',
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice.faktura', ['invoice' => $invoiceData])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'Faktura-' . str_replace('/', '-', $document->number ?? $id) . '.pdf';

        return new \Illuminate\Http\Response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}

