<?php

declare(strict_types=1);

namespace App\Http\Controllers\Father;

use App\Http\Controllers\Controller;
use App\Models\GlsInvoiceDocument;
use App\Models\GlsPaymentTransaction;
use App\Services\Invoice\InvoiceData;
use App\Services\Invoice\InvoiceLineItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $studentId = \Illuminate\Support\Facades\Auth::guard('student')->id();

        $transactions = GlsPaymentTransaction::query()
            ->where('student_id', '=', $studentId)
            ->orderByDesc('paid_at')
            ->get();

        return view('father.payment', [
            'transactions' => $transactions,
        ]);
    }

    public function process(Request $request): View
    {
        return view('father.payment_process');
    }

    public function success(): View
    {
        return view('father.payment_success');
    }

    public function fail(): View
    {
        return view('father.payment_fail');
    }

    /**
     * Download invoice PDF.
     * If pdf_path exists in storage → stream it.
     * If not → generate on-the-fly from DB data.
     */
    public function downloadInvoice(Request $request, int $id): \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
    {
        $studentId = \Illuminate\Support\Facades\Auth::guard('student')->id();

        $document = GlsInvoiceDocument::query()
            ->where('id', '=', $id)
            ->where('student_id', '=', $studentId) // Security: only own invoices
            ->firstOrFail();

        // Case 1: PDF already generated and stored
        if ($document->pdf_path && Storage::disk('private')->exists($document->pdf_path)) {
            $filename = 'Faktura-' . str_replace('/', '-', $document->number ?? (string)$id) . '.pdf';

            return Storage::disk('private')->download($document->pdf_path, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Case 2: Generate PDF on-the-fly from document data
        $student = $document->student;

        $lineItem = new InvoiceLineItem(
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

        $buyerName = trim(($student->parent_name ?? $student->name ?? '') . ' ' . ($student->parent_surname ?? $student->surname ?? ''));
        $buyerAddress = trim(($student->address ?? '') . ', ' . ($student->zip ?? '') . ' ' . ($student->city ?? ''));

        $invoiceData = new InvoiceData(
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

        $pdf = Pdf::loadView('pdf.invoice.faktura', ['invoice' => $invoiceData])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'Faktura-' . str_replace('/', '-', $document->number ?? (string)$id) . '.pdf';

        return new Response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
