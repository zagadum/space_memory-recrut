<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Models\GlsInvoiceDocument;
use App\Models\GlsPaymentTransaction;
use Illuminate\Support\Facades\Log;

final readonly class InvoiceGeneratorService
{
    public function __construct(
        private InvoiceNumberGenerator $numberGenerator,
        private InvoicePdfRenderer     $pdfRenderer,
    ) {}

    public function generateForTransaction(GlsPaymentTransaction $transaction): GlsInvoiceDocument
    {
        // Idempotency guard
        $existing = GlsInvoiceDocument::query()
            ->where('transaction_id', $transaction->id)
            ->where('document_type', 'invoice')
            ->first();

        if ($existing !== null) {
            Log::channel('invoice')->info('Invoice already exists, skipping', [
                'transaction_id' => $transaction->id,
                'document_id'    => $existing->id,
            ]);
            return $existing;
        }

        $now       = now();
        $docNumber = $this->numberGenerator->next('F', $now);
        $student   = $transaction->student;
        $project   = $transaction->project;
        $amount    = (float) $transaction->amount;

        // Educational services → VAT exempt ("zw.")
        $lineItem = new InvoiceLineItem(
            lp:         1,
            nazwa:      $this->buildServiceName($project?->name, $now),
            quantity:   1,
            unit:       'szt.',
            unitPrice:  $amount,
            vatRate:    'zw.',
            vatAmount:  0.0,
            totalNet:   $amount,
            totalGross: $amount,
        );

        $invoiceData = new InvoiceData(
            documentNumber: $docNumber,
            issueDate:      $now->format('Y-m-d'),
            saleDate:       $transaction->paid_at?->format('Y-m-d') ?? $now->format('Y-m-d'),
            buyerName:      $this->buildBuyerName($student),
            buyerAddress:   $this->buildBuyerAddress($student),
            buyerNip:       $student->nip ?? null,
            items:          [$lineItem],
            currency:       strtoupper((string) ($transaction->currency ?? 'PLN')),
            bankAccount:    'PL 00 0000 0000 0000 0000 0000 0000', // TODO: load from company settings
        );

        $pdfPath = $this->pdfRenderer->renderAndStore($invoiceData);

        // --- Persist document ---
        $lastDay = (clone $now)->endOfMonth();

        $document = GlsInvoiceDocument::query()->create([
            'student_id'        => $transaction->student_id,
            'transaction_id'    => $transaction->id,
            'project_id'        => $transaction->project_id,
            'document_type'     => 'invoice',
            'number'            => $docNumber,
            'title'             => $lineItem->nazwa,
            'issue_date'        => $now->toDateString(),
            'service_date_from' => $now->copy()->startOfMonth()->toDateString(),
            'service_date_to'   => $lastDay->toDateString(),
            'amount_net'        => $lineItem->totalNet,
            'amount_gross'      => $lineItem->totalGross,
            'currency'          => $invoiceData->currency,
            'ksef_status'       => 'pending',
            'pdf_path'          => $pdfPath,
        ]);

        Log::channel('invoice')->info('Invoice generated', [
            'document_id'    => $document->id,
            'number'         => $docNumber,
            'transaction_id' => $transaction->id,
            'pdf_path'       => $pdfPath,
        ]);

        return $document;
    }

    private function buildServiceName(?string $projectName, \DateTimeInterface $date): string
    {
        $monthNames = [
            1 => 'styczeń',     2 => 'luty',        3 => 'marzec',
            4 => 'kwiecień',    5 => 'maj',         6 => 'czerwiec',
            7 => 'lipiec',      8 => 'sierpień',    9 => 'wrzesień',
            10 => 'październik', 11 => 'listopad',  12 => 'grudzień',
        ];

        $month = $monthNames[(int) $date->format('n')] ?? '';
        $year  = $date->format('Y');
        $name  = $projectName ?? 'Kurs rozwojowy';

        return "{$name} — {$month} {$year}";
    }

    private function buildBuyerName(object $student): string
    {
        // Minor → parent is the legal buyer
        if (!empty($student->parent_name) && !empty($student->parent_surname)) {
            return trim($student->parent_name . ' ' . $student->parent_surname);
        }

        return trim(($student->name ?? '') . ' ' . ($student->surname ?? ''));
    }

    private function buildBuyerAddress(object $student): string
    {
        $parts = [];

        $street = trim(($student->address ?? '') . ' ' . ($student->apartment ?? ''));
        if ($street !== '') {
            $parts[] = $street;
        }

        $cityLine = trim(($student->zip ?? '') . ' ' . ($student->city ?? ''));
        if ($cityLine !== '') {
            $parts[] = $cityLine;
        }

        if (!empty($student->country) && strtolower((string) $student->country) !== 'polska') {
            $parts[] = $student->country;
        }

        return implode(', ', $parts);
    }
}
