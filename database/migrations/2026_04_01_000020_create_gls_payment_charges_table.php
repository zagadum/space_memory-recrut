<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_payment_charges', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('group_id')->nullable();

            // Тип начисления
            $table->enum('charge_type', [
                'monthly_start',      // первый месяц при старте группы
                'monthly_alignment',  // выравнивание при добавлении в середине месяца
                'monthly_standard',   // стандартная ежемесячная оплата
                'platform',           // доступ к платформе
                'extra_lesson',       // дополнительное занятие
                'bonus_class',        // бонусное занятие
                'material',           // учебные материалы
                'manual_adjustment',  // ручная корректировка
                'refund_adjustment',  // возврат
            ]);

            // Период начисления
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month'); // 1–12

            // Суммы
            $table->decimal('base_amount',     10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount',    10, 2); // base - discount

            // Статус
            $table->enum('status', [
                'draft',
                'pending',
                'paid',
                'partially_paid',
                'overdue',
                'cancelled',
                'paused',
                'overpayment',
                'closed',
                'refunded',
            ])->default('pending');

            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('gls_projects')->onDelete('restrict');

            $table->index(['student_id', 'period_year', 'period_month']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_payment_charges');
    }
};
