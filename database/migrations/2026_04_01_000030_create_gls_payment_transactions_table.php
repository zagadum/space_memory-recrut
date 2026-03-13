<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_payment_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('project_id');

            // Способ оплаты
            $table->enum('provider', [
                'imoje',          // Imoje (ING Bank) — основной шлюз
                'cash',           // наличные
                'bank_transfer',  // банковский перевод
                'manual',         // ручная фиксация менеджером
            ]);

            // Направление: in = платёж от ученика, out = возврат
            $table->enum('direction', ['in', 'out'])->default('in');

            $table->decimal('amount',   10, 2);
            $table->char('currency',     3)->default('PLN');

            $table->enum('status', [
                'pending',    // ожидает подтверждения
                'completed',  // подтверждён
                'failed',     // ошибка
                'refunded',   // возвращён
                'cancelled',  // отменён
            ])->default('pending');

            // ID транзакции от провайдера (Imoje и др.)
            $table->string('provider_transaction_id')->nullable()->unique();

            // Сырой webhook payload для отладки
            $table->json('provider_payload')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('gls_projects')->onDelete('restrict');

            $table->index(['student_id', 'status']);
            $table->index(['provider', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_payment_transactions');
    }
};
