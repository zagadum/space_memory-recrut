<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Таблица создаётся при вызове POST /api/v1/salary/{id}/dispute
// Одновременно статус в gls_salary_calculations меняется на 'disputed'

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_salary_disputes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('salary_calculation_id');
            $table->unsignedBigInteger('teacher_id');   // кто оспаривает
            $table->unsignedBigInteger('project_id');

            $table->text('reason'); // обязателен, min:3 max:2000

            $table->enum('status', [
                'open',      // создан, ожидает рассмотрения
                'resolved',  // разрешён
                'rejected',  // отклонён
            ])->default('open');

            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_comment')->nullable();

            $table->timestamps();

            $table->foreign('salary_calculation_id')
                  ->references('id')
                  ->on('gls_salary_calculations')
                  ->onDelete('cascade');

            $table->foreign('project_id')
                  ->references('id')
                  ->on('gls_projects')
                  ->onDelete('restrict');

            $table->index(['salary_calculation_id', 'status']);
            $table->index(['teacher_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_salary_disputes');
    }
};
