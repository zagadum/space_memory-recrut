<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\InvoiceGeneratedEvent;
use App\Models\GlsPaymentTransaction;
use App\Services\Invoice\InvoiceGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class GeneratePdfInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly GlsPaymentTransaction $transaction,
    ) {
        $this->onQueue('invoices');
    }

    public function handle(InvoiceGeneratorService $service): void
    {
        $document = $service->generateForTransaction($this->transaction);

        \App\Events\InvoiceGeneratedEvent::dispatch($document);
    }

    public function failed(\Throwable $exception): void
    {
        Log::channel('invoice')->error('GeneratePdfInvoiceJob FAILED', [
            'transaction_id' => $this->transaction->id,
            'error'          => $exception->getMessage(),
            'trace'          => $exception->getTraceAsString(),
        ]);
    }
}
