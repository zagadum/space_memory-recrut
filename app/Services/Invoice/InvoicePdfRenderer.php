<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

final readonly class InvoicePdfRenderer
{
    public function renderAndStore(InvoiceData $data): string
    {
        $pdf = Pdf::loadView('father.invoice.pdf.faktura', ['invoice' => $data])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = str_replace(['/', ' '], '-', $data->documentNumber) . '.pdf';
        $year     = date('Y', strtotime($data->issueDate));
        $month    = str_pad((string) date('n', strtotime($data->issueDate)), 2, '0', STR_PAD_LEFT);
        $path     = "invoices/{$year}/{$month}/{$filename}";

        Storage::disk('private')->put($path, $pdf->output());

        return $path;
    }
}
