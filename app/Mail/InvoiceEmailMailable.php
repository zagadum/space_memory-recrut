<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\GlsInvoiceDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

final class InvoiceEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly GlsInvoiceDocument $document,
        public readonly string $studentName,
    ) {}

    public function envelope(): Envelope
    {
        $number = $this->document->number ?? 'DRAFT';

        return new Envelope(
            subject: "Faktura {$number} — Global Leaders Skills",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'studentName'    => $this->studentName,
                'invoiceNumber'  => $this->document->number ?? 'DRAFT',
                'issueDate'      => $this->document->issue_date?->format('d.m.Y') ?? '',
                'amount'         => number_format((float) $this->document->amount_gross, 2, ',', ' '),
                'currency'       => $this->document->currency ?? 'PLN',
                'serviceName'    => $this->document->title ?? 'Usługa edukacyjna',
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (!$this->document->pdf_path || !Storage::disk('private')->exists($this->document->pdf_path)) {
            return [];
        }

        $filename = 'Faktura-' . str_replace('/', '-', $this->document->number ?? 'invoice') . '.pdf';

        return [
            Attachment::fromStorageDisk('private', $this->document->pdf_path)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}
