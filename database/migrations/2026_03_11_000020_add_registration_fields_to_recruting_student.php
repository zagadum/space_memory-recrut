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
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        Schema::table('recruting_student', function (Blueprint $table) {
            if (!Schema::hasColumn('recruting_student', 'parent_name')) {
                $table->string('parent_name')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'parent_surname')) {
                $table->string('parent_surname')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'parent_phone')) {
                $table->string('parent_phone')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'parent_passport')) {
                $table->string('parent_passport')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'dob')) {
                $table->date('dob')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'zip')) {
                $table->string('zip')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'apartment')) {
                $table->string('apartment')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'photo_consent')) {
                $table->boolean('photo_consent')->default(0);
            }
            if (!Schema::hasColumn('recruting_student', 'reg_comment')) {
                $table->text('reg_comment')->nullable();
            }
            if (!Schema::hasColumn('recruting_student', 'terms_accepted')) {
                $table->boolean('terms_accepted')->default(0);
            }
            if (!Schema::hasColumn('recruting_student', 'privacy_accepted')) {
                $table->boolean('privacy_accepted')->default(0);
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
            $columns = [
                'parent_name',
                'parent_surname',
                'parent_phone',
                'parent_passport',
                'dob',
                'country',
                'city',
                'address',
                'zip',
                'apartment',
                'photo_consent',
                'reg_comment',
                'terms_accepted',
                'privacy_accepted',
            ];

            $existingColumns = array_values(array_filter($columns, static fn (string $column): bool => Schema::hasColumn('recruting_student', $column)));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
