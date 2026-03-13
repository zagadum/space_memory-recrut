<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\GlsPaymentTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class PaymentConfirmedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly GlsPaymentTransaction $transaction,
    ) {}
}
