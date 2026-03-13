<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_invoice_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('project_id');

            // Документ может быть привязан к транзакции и/или начислению
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->unsignedBigInteger('charge_id')->nullable();

            $table->enum('document_type', [
                'invoice',          // счёт-фактура
                'receipt',          // квитанция
                'act',              // акт выполненных работ
                'credit_note',      // кредит-нота (при возврате)
                'proforma',         // проформа-инвойс
            ]);

            $table->string('number', 50)->unique(); // GLS-2026-001

            $table->date('issue_date');
            $table->date('service_date_from')->nullable();
            $table->date('service_date_to')->nullable();

            $table->string('title', 255)->nullable();

            $table->decimal('amount_net',   10, 2);
            $table->decimal('amount_gross', 10, 2);
            $table->char('currency', 3)->default('PLN');

            // Интеграция с польской системой KSeF (электронные счета)
            $table->enum('ksef_status', [
                'not_applicable', // не применяется
                'pending',        // ожидает отправки
                'sent',           // отправлено
                'accepted',       // принято
                'rejected',       // отклонено
            ])->default('not_applicable');

            $table->string('ksef_reference')->nullable(); // ссылка в KSeF

            $table->string('pdf_path')->nullable(); // путь к PDF в storage

            $table->json('meta')->nullable(); // доп. данные

            $table->timestamps();

            $table->foreign('project_id')
                  ->references('id')
                  ->on('gls_projects')
                  ->onDelete('restrict');

            $table->foreign('transaction_id')
                  ->references('id')
                  ->on('gls_payment_transactions')
                  ->onDelete('set null');

            $table->foreign('charge_id')
                  ->references('id')
                  ->on('gls_payment_charges')
                  ->onDelete('set null');

            $table->index(['student_id', 'document_type']);
            $table->index(['ksef_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_invoice_documents');
    }
};
