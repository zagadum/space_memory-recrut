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
            if (!Schema::hasColumn('recruting_student', 'status')) {
                $table->string('status')->default('new')->after('email');
            }
            // Воронка: 'new' | 'registered' | 'paid' | 'transferred' | 'archived'

            if (!Schema::hasColumn('recruting_student', 'phone')) {
                $table->string('phone')->nullable()->after('status');
            }
            if (!Schema::hasColumn('recruting_student', 'subject')) {
                $table->string('subject')->nullable()->after('phone');
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
            $columns = array_values(array_filter(
                ['status', 'phone', 'subject'],
                static fn (string $column): bool => Schema::hasColumn('recruting_student', $column)
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
