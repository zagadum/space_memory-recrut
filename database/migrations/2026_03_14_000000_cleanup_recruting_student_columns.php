<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        // 1. Add missing columns first
        Schema::table('recruting_student', function (Blueprint $table) {
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
        });

        // 2. Migrate legacy data
        // We use query builder to avoid model issues during migration
        DB::table('recruting_student')->chunkById(100, function ($students) {
            foreach ($students as $student) {
                $update = [];

                // Migrate phone
                if (empty($student->parent_phone) && !empty($student->phone)) {
                    $update['parent_phone'] = $student->phone;
                }
                if (empty($student->parent_phone) && !empty($student->parent1_phone)) {
                    $update['parent_phone'] = $student->parent1_phone;
                }

                // Migrate locality
                if (empty($student->city) && !empty($student->locality)) {
                    $update['city'] = $student->locality;
                }

                // Migrate parent names
                if (empty($student->parent_name) && !empty($student->parent1_lastname)) {
                    $update['parent_name'] = $student->parent1_lastname;
                }
                if (empty($student->parent_surname) && !empty($student->parent1_surname)) {
                    $update['parent_surname'] = $student->parent1_surname;
                }

                // Migrate country if empty
                if (empty($student->country) && !empty($student->parent1_phone_country)) {
                    $update['country'] = $student->parent1_phone_country;
                }

                if (!empty($update)) {
                    DB::table('recruting_student')->where('id', $student->id)->update($update);
                }
            }
        });

        // 3. Drop redundant and legacy columns
        Schema::table('recruting_student', function (Blueprint $table) {
            $colsToDrop = [
                'phone',
                'locality',
                'parent1_surname',
                'parent1_lastname',
                'parent1_patronymic',
                'parent1_phone',
                'parent1_phone_country',
                'parent2_surname',
                'parent2_first_name',
                'parent2_patronymic',
                'parent2_phone',
                'parent2_phone_country',
                'parent3_surname',
                'parent3_first_name',
                'parent3_patronymic',
                'parent3_phone',
                'parent3_phone_country',
            ];

            foreach ($colsToDrop as $col) {
                if (Schema::hasColumn('recruting_student', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        Schema::table('recruting_student', function (Blueprint $table) {
            // Restore missing columns if needed, but dropping them is usually enough for down()
            $table->dropColumn([
                'data_processing_accepted',
                'urgent_start_accepted',
                'recording_consent_accepted',
                'marketing_consent_accepted',
            ]);

            // Note: Data migration and column drops are harder to reverse completely
            // without knowing original state, so we typically leave them or re-add as nullable
        });
    }
};
