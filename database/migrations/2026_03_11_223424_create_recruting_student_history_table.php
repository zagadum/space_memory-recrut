<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('recruting_student_history')) {
            return;
        }

        Schema::create('recruting_student_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('student_id');
            $table->string('event', 100);
            $table->string('detail', 500)->nullable();
            $table->string('changed_by', 100)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('student_id')
                  ->references('id')
                  ->on('recruting_student')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruting_student_history');
    }
};
