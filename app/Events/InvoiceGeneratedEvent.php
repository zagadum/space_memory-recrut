<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\GlsInvoiceDocument;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class InvoiceGeneratedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly GlsInvoiceDocument $document,
    ) {}
}
