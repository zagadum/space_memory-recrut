<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Один платёж может закрывать несколько начислений.
// Эта таблица распределяет сумму транзакции по конкретным начислениям.
//
// Пример: транзакция 500 PLN закрывает:
//   - charge #12 (monthly_standard) на 450 PLN
//   - charge #13 (material)         на 50  PLN

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_payment_allocations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('charge_id');
            $table->decimal('amount', 10, 2);

            $table->timestamps();

            $table->foreign('transaction_id')
                  ->references('id')
                  ->on('gls_payment_transactions')
                  ->onDelete('cascade');

            $table->foreign('charge_id')
                  ->references('id')
                  ->on('gls_payment_charges')
                  ->onDelete('restrict');

            // Один платёж не может дважды закрывать одно начисление
            $table->unique(['transaction_id', 'charge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_payment_allocations');
    }
};
