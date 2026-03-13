<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\InvoiceEmailMailable;
use App\Models\GlsInvoiceDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 120;

    public function __construct(
        public readonly GlsInvoiceDocument $document,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $student = $this->document->student;

        if (!$student || empty($student->email)) {
            Log::channel('invoice')->warning('SendInvoiceEmailJob: no student email', [
                'document_id' => $this->document->id,
            ]);
            return;
        }

        // Determine recipient name (parent if available, else student)
        $name = trim(($student->parent_name ?? $student->name ?? '') . ' ' . ($student->parent_surname ?? $student->surname ?? ''));

        Mail::to($student->email)->send(
            new InvoiceEmailMailable($this->document, $name)
        );

        Log::channel('invoice')->info('Invoice email sent', [
            'document_id' => $this->document->id,
            'number'      => $this->document->number,
            'email'       => $student->email,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('invoice')->error('SendInvoiceEmailJob FAILED', [
            'document_id' => $this->document->id,
            'error'       => $exception->getMessage(),
        ]);
    }
}
