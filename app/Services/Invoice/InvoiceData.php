<?php

declare(strict_types=1);

namespace App\Services\Invoice;

final readonly class InvoiceData
{
    /**
     * @param list<InvoiceLineItem> $items
     */
    public function __construct(
        public string  $documentNumber,
        public string  $issueDate,
        public string  $saleDate,
        public string  $buyerName,
        public string  $buyerAddress,
        public ?string $buyerNip,
        public array   $items,
        public string  $currency = 'PLN',
        public ?string $paymentMethod = 'przelew',
        public ?string $paymentDueDate = null,
        public ?string $bankAccount = null,
    ) {}
}
