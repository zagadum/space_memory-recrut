<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PaymentConfirmedEvent;
use App\Jobs\GeneratePdfInvoiceJob;

final class GenerateInvoiceForPayment
{
    public function handle(PaymentConfirmedEvent $event): void
    {
        GeneratePdfInvoiceJob::dispatch($event->transaction);
    }
}
