<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\InvoiceGeneratedEvent;
use App\Jobs\SendInvoiceEmailJob;

final class SendInvoiceEmailListener
{
    public function handle(InvoiceGeneratedEvent $event): void
    {
        SendInvoiceEmailJob::dispatch($event->document);
    }
}
