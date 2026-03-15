<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Models\GlsInvoiceCounter;
use App\Models\GlsProject;
use Illuminate\Support\Facades\DB;

final class InvoiceGeneratorService
{
    /**
     * Generate an invoice document and its PDF for a specific transaction.
     */
    public function generateForTransaction(\App\Models\GlsPaymentTransaction $transaction): \App\Models\GlsInvoiceDocument
    {
        $project = $transaction->project;
        $student = $transaction->student;
        $serviceFrom = $transaction->paid_at?->copy()->startOfDay() ?? now()->startOfDay();
        $serviceTo = $serviceFrom->copy()->addMonths(max((int) ($transaction->months ?? 1), 1))->subDay();
        $documentTitle = $transaction->title ?: 'Usługa edukacyjna - pakiet zajęć';

        return DB::transaction(function () use ($transaction, $project, $student, $serviceFrom, $serviceTo, $documentTitle) {
            /** @var \App\Models\GlsInvoiceDocument \$document */
            $document = \App\Models\GlsInvoiceDocument::firstOrNew([
                'transaction_id' => $transaction->id,
                'document_type' => 'invoice',
            ]);

            if (!$document->exists) {
                $document->fill([
                    'student_id'   => $student->id,
                    'project_id'   => $project->id,
                    'document_type' => 'invoice',
                    'issue_date'    => now()->format('Y-m-d'),
                    'sale_date'     => $transaction->paid_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'service_date_from' => $serviceFrom->format('Y-m-d'),
                    'service_date_to'   => $serviceTo->format('Y-m-d'),
                    'title'         => $documentTitle,
                    'currency'      => $transaction->currency,
                    'amount_gross'  => $transaction->amount,
                    'amount_net'    => round((float) $transaction->amount / 1.23, 2), // Example: assume 23% VAT
                    'meta'          => [
                        'payment_plan_id' => $transaction->payment_plan_id,
                        'months' => $transaction->months,
                        'lessons' => $transaction->lessons,
                    ],
                ]);
            }

            if (!$document->number) {
                $document->number = $this->getNextInvoiceNumber($project, (string) $document->issue_date);
            }

            $document->save();

            // Prepare InvoiceData for PDF rendering
            $items = [
                new InvoiceLineItem(
                    lp: 1,
                    nazwa: $documentTitle,
                    quantity: 1,
                    unit: 'szt.',
                    unitPrice: (float) $document->amount_net,
                    vatRate: '23%',
                    vatAmount: (float) ($document->amount_gross - $document->amount_net),
                    totalNet: (float) $document->amount_net,
                    totalGross: (float) $document->amount_gross
                ),
            ];

            $invoiceData = new InvoiceData(
                documentNumber: $document->number,
                issueDate: (string) $document->issue_date,
                saleDate: (string) $document->sale_date,
                buyerName: trim(($student->parent_full_name ?? '') ?: ($student->full_name ?? 'Klient')),
                buyerAddress: trim(($student->address ?? '') . ', ' . ($student->zip ?? '') . ' ' . ($student->city ?? '')) ?: ($student->city ?? 'Warszawa'),
                buyerNip: null,
                items: $items,
                currency: $document->currency
            );

            // Render and store PDF
            $renderer = new InvoicePdfRenderer();
            $pdfPath = $renderer->renderAndStore($invoiceData);

            $document->update(['pdf_path' => $pdfPath]);

            return $document;
        });
    }

    /**
     * Get the next sequential invoice number and increment the counter.
     */
    public function getNextInvoiceNumber(GlsProject $project, string|\DateTimeInterface $issueDate): string
    {
        $time  = is_string($issueDate) ? strtotime($issueDate) : $issueDate->getTimestamp();
        $year  = (int) date('Y', $time);
        $month = (int) date('n', $time);

        return DB::transaction(function () use ($project, $year, $month) {
            $counter = GlsInvoiceCounter::where([
                'project_id'   => $project->id,
                'period_year'  => $year,
                'period_month' => $month,
            ])->lockForUpdate()->first();

            if (!$counter) {
                $counter = GlsInvoiceCounter::create([
                    'project_id'   => $project->id,
                    'period_year'  => $year,
                    'period_month' => $month,
                    'last_number'  => 0,
                ]);
            }

            $currentNumber = $counter->last_number + 1;
            $counter->last_number = $currentNumber;
            $counter->updated_at = now();
            $counter->save();

            // Format: FV/{Project}/{YYYY}/{MM}/{Counter}
            return sprintf(
                'FV/%s/%d/%02d/%04d',
                strtoupper($project->code),
                $year,
                $month,
                $currentNumber
            );
        });
    }
}
