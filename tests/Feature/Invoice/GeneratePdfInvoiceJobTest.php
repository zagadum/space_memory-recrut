<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice;

use App\Events\InvoiceGeneratedEvent;
use App\Events\PaymentConfirmedEvent;
use App\Jobs\GeneratePdfInvoiceJob;
use App\Models\GlsInvoiceDocument;
use App\Models\GlsPaymentTransaction;
use App\Models\GlsProject;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneratePdfInvoiceJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create temporary student table for SQLite tests since migration is missing
        \Illuminate\Support\Facades\Schema::create('student', function ($table) {
            $table->id();
            $table->string('surname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }

    public function test_event_dispatches_job(): void
    {
        Queue::fake();

        $tx = $this->createTestTransaction();
        PaymentConfirmedEvent::dispatch($tx);

        Queue::assertPushed(GeneratePdfInvoiceJob::class, fn ($j) => $j->transaction->id === $tx->id);
    }

    public function test_job_creates_document_and_pdf(): void
    {
        Storage::fake('private');
        Event::fake([InvoiceGeneratedEvent::class]);

        $tx  = $this->createTestTransaction();
        $job = new GeneratePdfInvoiceJob($tx);
        $job->handle(app(\App\Services\Invoice\InvoiceGeneratorService::class));

        $this->assertDatabaseHas('gls_invoice_documents', [
            'transaction_id' => $tx->id,
            'document_type'  => 'invoice',
        ]);

        $doc = GlsInvoiceDocument::query()->where('transaction_id', $tx->id)->first();
        Storage::disk('private')->assertExists($doc->pdf_path);
        Event::assertDispatched(InvoiceGeneratedEvent::class);
    }

    public function test_job_is_idempotent(): void
    {
        Storage::fake('private');
        Event::fake([InvoiceGeneratedEvent::class]);

        $tx      = $this->createTestTransaction();
        $service = app(\App\Services\Invoice\InvoiceGeneratorService::class);

        (new GeneratePdfInvoiceJob($tx))->handle($service);
        (new GeneratePdfInvoiceJob($tx))->handle($service);

        $this->assertSame(1, GlsInvoiceDocument::query()
            ->where('transaction_id', $tx->id)
            ->where('document_type', 'invoice')
            ->count());
    }

    private function createTestTransaction(): GlsPaymentTransaction
    {
        $student = Student::query()->create([
            'surname' => 'Jan', 'lastname' => 'Kowalski',
            'email' => 'jan' . uniqid() . '@test.pl', 'password' => bcrypt('x'),
        ]);

        $project = GlsProject::query()->create([
            'code' => 'space_memory',
            'name' => 'Space Memory',
        ]);

        return GlsPaymentTransaction::query()->create([
            'student_id' => $student->id, 'project_id' => $project->id,
            'provider' => 'imoje', 'direction' => 'in',
            'amount' => 440.00, 'currency' => 'PLN',
            'status' => 'completed', 'paid_at' => now(),
        ]);
    }
}
