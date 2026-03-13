<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice;

use App\Services\Invoice\InvoiceNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceNumberGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new InvoiceNumberGenerator();
    }

    public function test_generates_first_number_of_month(): void
    {
        $number = $this->generator->next('F', new \DateTimeImmutable('2026-03-15'));

        $this->assertSame('F/0001/03/2026', $number);
        $this->assertDatabaseHas('gls_invoice_counters', [
            'prefix' => 'F', 'year' => 2026, 'month' => 3, 'last_number' => 1,
        ]);
    }

    public function test_generates_sequential_numbers(): void
    {
        $date = new \DateTimeImmutable('2026-03-15');

        $this->assertSame('F/0001/03/2026', $this->generator->next('F', $date));
        $this->assertSame('F/0002/03/2026', $this->generator->next('F', $date));
        $this->assertSame('F/0003/03/2026', $this->generator->next('F', $date));
    }

    public function test_resets_counter_on_new_month(): void
    {
        $this->generator->next('F', new \DateTimeImmutable('2026-03-15'));
        $this->generator->next('F', new \DateTimeImmutable('2026-03-15'));

        $this->assertSame(
            'F/0001/04/2026',
            $this->generator->next('F', new \DateTimeImmutable('2026-04-01'))
        );
    }

    public function test_different_prefixes_are_independent(): void
    {
        $date = new \DateTimeImmutable('2026-03-15');

        $this->assertSame('F/0001/03/2026', $this->generator->next('F', $date));
        $this->assertSame('FK/0001/03/2026', $this->generator->next('FK', $date));
    }
}
