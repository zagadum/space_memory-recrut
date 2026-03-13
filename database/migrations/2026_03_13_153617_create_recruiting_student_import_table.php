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
        if (Schema::hasTable('recruiting_student_import')) {
            return;
        }

        Schema::create('recruiting_student_import', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();           // предмет
            $table->string('source')->nullable();             // откуда: 'csv_import', 'manual', 'landing'
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('campaign_id')->nullable()->index('idx_rsi_camp_id'); // ID кампании рассылки
            $table->string('token', 64)->unique('uni_rsi_token');            // уникальный токен для ссылки
            $table->string('status')->default('pending');     // pending | sent | delivered | clicked | converted | failed
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('email_opened_at')->nullable();
            $table->timestamp('link_clicked_at')->nullable();
            $table->timestamp('converted_at')->nullable();    // когда перенесён в recruting_student
            $table->unsignedBigInteger('converted_student_id')->nullable(); // FK после конверсии
            $table->text('error_message')->nullable();        // если email failed
            $table->json('meta')->nullable();                 // доп. данные из CSV
            $table->timestamps();

            $table->index(['campaign_id', 'status'], 'idx_rsi_camp_status');
            $table->index('email', 'idx_rsi_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruiting_student_import');
    }
};
