<?php

declare(strict_types=1);

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsInvoiceDocument;
use App\Models\GlsPaymentTransaction;
use App\Models\GlsProject;
use App\Services\ImojePaymentService;
use App\Services\Invoice\InvoiceData;
use App\Services\Invoice\InvoiceLineItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $studentId = Auth::guard('recruting_student')->id();
        $student = \App\Models\RecrutingStudent::find($studentId);

        if (!$student) {
            abort(403);
        }

        $payments = GlsPaymentTransaction::query()
            ->where('student_id', '=', $studentId)
            ->orderByDesc('created_at')
            ->get();

        $contract = (object)[
            'signed' => true, // Assuming true for now, as real contract status logic is missing
        ];

        $periods = [
            [
                'months' => 1,
                'lessons' => 4,
                'price' => 440,
                'old' => 490,
                'popular' => false,
                'save' => 0,
            ],
            [
                'months' => 3,
                'lessons' => 12,
                'price' => 1180,
                'old' => 1470,
                'popular' => true,
                'save' => 290,
            ],
            [
                'months' => 9,
                'lessons' => 36,
                'price' => 3160,
                'old' => 4410,
                'popular' => false,
                'save' => 1250,
            ],
        ];

        return view('father.payment', compact('student', 'contract', 'payments', 'periods'));
    }

    public function createOrder(Request $request)
    {
        $studentId = Auth::guard('recruting_student')->id();
        $student   = DB::table('recruting_student')->where('id', $studentId)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 403);
        }

        $validated = $request->validate([
            'project_code' => ['sometimes', 'string', 'in:space_memory,indigo'],
            'amount'       => ['required', 'numeric', 'min:1'],
        ]);

        $projectCode = $validated['project_code'] ?? 'space_memory';

        $project = GlsProject::query()
            ->where('code', $projectCode)
            ->firstOrFail();

        // Create transaction in DB
        $transaction = GlsPaymentTransaction::query()->create([
            'student_id'  => (int)$studentId,
            'project_id'  => $project->id,
            'provider'    => 'imoje',
            'direction'   => 'in',
            'amount'      => (float)$validated['amount'],
            'currency'    => 'PLN',
            'status'      => 'pending',
        ]);

        // Build iMoje form fields
        $hashMethod = 'sha256';
        $serviceKey = (string)env('IMOJE_SERVICE_KEY');

        $fields = [
            'merchantId'          => (string)env('IMOJE_MERCHANT_ID'),
            'serviceId'           => (string)env('IMOJE_SERVICE_ID'),
            'amount'              => (int) ($validated['amount'] * 100), // grosze
            'currency'            => 'PLN',
            'customerFirstName'   => $student->name ?? '',
            'customerLastName'    => $student->surname ?? '',
            'customerEmail'       => $student->email ?? '',
            'customerPhone'       => $student->parent_phone ?? '',
            'orderId'             => (string) $transaction->id,
            'customerId'          => (string) $studentId,
            'orderDescription'    => $project->name . ' — ' . now()->translatedFormat('F Y'),
            'locale'              => 'pl',
            'urlSuccess'          => url('/father/payment-success'),
            'urlFailure'          => url('/father/payment-fail'),
            'urlNotification'     => url('/payments/imoje/webhook'),
        ];

        $imojeService = app(ImojePaymentService::class);
        $signature    = $imojeService->createSignature($fields, $serviceKey, $hashMethod) . ';' . $hashMethod;
        $fields['signature'] = $signature;

        $payUrl = env('IMOJE_PAY_URL', 'https://sandbox.paywall.imoje.pl/payment');

        if ($request->expectsJson() || $request->isJson()) {
            return response()->json([
                'pay_url' => $payUrl,
                'fields'  => $fields,
            ]);
        }

        return view('father.payment_redirect', [
            'fields' => $fields,
            'payUrl' => $payUrl,
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
        $studentId = \Illuminate\Support\Facades\Auth::guard('recruting_student')->id();

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
