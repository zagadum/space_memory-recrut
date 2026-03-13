<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_lesson_additional', function (Blueprint $table) {
            $table->id();

            // Связь с заданием ученика в группе (из Space Memory)
            $table->unsignedBigInteger('student_group_task_id')->nullable();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();

            $table->date('lesson_date');

            $table->enum('additional_type', [
                'consultation',       // консультация
                'missed_replacement', // замена пропущенного занятия
                'extra',              // дополнительное занятие
                'olympiad_prep',      // подготовка к олимпиаде
                'individual',         // индивидуальное занятие
            ]);

            $table->decimal('base_amount',     10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_amount',    10, 2)->default(0.00);

            $table->enum('status', [
                'scheduled',  // запланировано
                'completed',  // проведено
                'cancelled',  // отменено
            ])->default('scheduled');

            $table->text('comment')->nullable();

            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('gls_projects')->onDelete('restrict');

            $table->index(['student_id', 'lesson_date']);
            $table->index(['teacher_id', 'lesson_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_lesson_additional');
    }
};
