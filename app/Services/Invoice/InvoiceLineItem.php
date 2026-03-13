<?php

declare(strict_types=1);

namespace App\Services\Invoice;

final readonly class InvoiceLineItem
{
    public function __construct(
        public int    $lp,
        public string $nazwa,
        public float  $quantity,
        public string $unit,
        public float  $unitPrice,
        public string $vatRate,
        public float  $vatAmount,
        public float  $totalNet,
        public float  $totalGross,
    ) {}
}
