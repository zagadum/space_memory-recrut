<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Добавление полей согласий для формы регистрации
     * ТОЛЬКО расширение recruting_student (не затрагивает эталон student.sql)
     */
    public function up(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            // Поля согласий (для формы регистрации)
            if (!Schema::hasColumn('recruting_student', 'data_processing_accepted')) {
                $table->boolean('data_processing_accepted')->default(0)->after('privacy_accepted');
            }
            if (!Schema::hasColumn('recruting_student', 'urgent_start_accepted')) {
                $table->boolean('urgent_start_accepted')->default(0)->after('data_processing_accepted');
            }
            if (!Schema::hasColumn('recruting_student', 'recording_consent_accepted')) {
                $table->boolean('recording_consent_accepted')->default(0)->after('urgent_start_accepted');
            }
            if (!Schema::hasColumn('recruting_student', 'marketing_consent_accepted')) {
                $table->boolean('marketing_consent_accepted')->default(0)->after('recording_consent_accepted');
            }

            // Будущее: поля для email-воронки
            if (!Schema::hasColumn('recruting_student', 'email_sent')) {
                $table->boolean('email_sent')->default(0)->after('marketing_consent_accepted');
            }
            if (!Schema::hasColumn('recruting_student', 'email_sent_at')) {
                $table->timestamp('email_sent_at')->nullable()->after('email_sent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->dropColumn([
                'data_processing_accepted',
                'urgent_start_accepted',
                'recording_consent_accepted',
                'marketing_consent_accepted',
                'email_sent',
                'email_sent_at',
            ]);
        });
    }
};
