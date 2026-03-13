<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_salary_calculations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('teacher_id'); // FK в Space Memory, здесь без constraint

            // Период расчёта
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month'); // 1–12

            // Компоненты зарплаты
            $table->unsignedInteger('base_subscriptions')->default(0);  // кол-во подписок
            $table->decimal('pct_subscriptions',     8, 4)->default(0); // % от подписок
            $table->decimal('substitutions_amount',  10, 2)->default(0.00); // замены
            $table->decimal('methodical_amount',     10, 2)->default(0.00); // методическая работа
            $table->decimal('individual_amount',     10, 2)->default(0.00); // индивидуальная работа
            $table->decimal('olympiad_amount',       10, 2)->default(0.00); // олимпиады
            $table->decimal('admin_duty_amount',     10, 2)->default(0.00); // административные
            $table->decimal('bonuses_amount',        10, 2)->default(0.00); // бонусы
            $table->decimal('trial_lessons_amount',  10, 2)->default(0.00); // пробные уроки
            $table->decimal('retention_bonus_amount',10, 2)->default(0.00); // удержание учеников

            $table->decimal('total', 10, 2)->default(0.00); // итого к выплате

            // Статусы точно по Sallery_API.md:
            // draft → confirmed → paid
            // draft → disputed (при оспаривании через POST /salary/{id}/dispute)
            $table->enum('status', [
                'draft',      // черновик, идёт расчёт
                'confirmed',  // подтверждено руководством
                'paid',       // выплачено
                'disputed',   // оспорено преподавателем
            ])->default('draft');

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Детальный breakdown в JSON для аудита
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('gls_projects')->onDelete('restrict');

            // Один расчёт на преподавателя за период
            $table->unique(['project_id', 'teacher_id', 'period_year', 'period_month'], 'gls_salary_calc_unq');

            $table->index(['teacher_id', 'period_year', 'period_month'], 'gls_salary_calc_idx');
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_salary_calculations');
    }
};
