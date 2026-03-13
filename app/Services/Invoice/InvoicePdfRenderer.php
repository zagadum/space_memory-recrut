<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

final readonly class InvoicePdfRenderer
{
    public function renderAndStore(InvoiceData $data): string
    {
        $path = $this->generatePath($data);
        Storage::disk('private')->put($path, $this->renderBinary($data));

        return $path;
    }

    public function renderBinary(InvoiceData $data): string
    {
        return Pdf::loadView('pdf.invoice.faktura', ['invoice' => $data])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->output();
    }

    private function generatePath(InvoiceData $data): string
    {
        $filename = str_replace(['/', ' '], '-', $data->documentNumber) . '.pdf';
        $year     = date('Y', strtotime($data->issueDate));
        $month    = str_pad((string) date('n', strtotime($data->issueDate)), 2, '0', STR_PAD_LEFT);
        
        return "invoices/{$year}/{$month}/{$filename}";
    }
}
