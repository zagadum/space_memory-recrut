<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use App\Models\GlsInvoiceCounter;
use Illuminate\Support\Facades\DB;

/**
 * Gap-free sequential invoice numbers with pessimistic locking.
 * Format: F/{NNNN}/{MM}/{YYYY} — resets each month.
 */
final readonly class InvoiceNumberGenerator
{
    public function next(string $prefix = 'F', ?\DateTimeInterface $date = null): string
    {
        $date  ??= now();
        $year  = (int) $date->format('Y');
        $month = (int) $date->format('n');

        return DB::transaction(function () use ($prefix, $year, $month): string {
            $counter = GlsInvoiceCounter::query()
                ->where('prefix', $prefix)
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if ($counter === null) {
                $counter = GlsInvoiceCounter::query()->create([
                    'prefix'      => $prefix,
                    'year'        => $year,
                    'month'       => $month,
                    'last_number' => 1,
                    'updated_at'  => now(),
                ]);

                $nextNumber = 1;
            } else {
                $nextNumber = $counter->last_number + 1;
                $counter->update([
                    'last_number' => $nextNumber,
                    'updated_at'  => now(),
                ]);
            }

            return sprintf('%s/%04d/%02d/%04d', $prefix, $nextNumber, $month, $year);
        });
    }
}
