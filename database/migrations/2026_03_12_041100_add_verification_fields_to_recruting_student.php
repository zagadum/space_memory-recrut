<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        Schema::table('recruting_student', function (Blueprint $table) {
            if (!Schema::hasColumn('recruting_student', 'verification_code')) {
                $table->string('verification_code', 4)->nullable()->after('api_token');
            }
            if (!Schema::hasColumn('recruting_student', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('verification_code');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('recruting_student')) {
            return;
        }

        Schema::table('recruting_student', function (Blueprint $table) {
            $columns = array_values(array_filter(
                ['verification_code', 'email_verified_at'],
                static fn (string $column): bool => Schema::hasColumn('recruting_student', $column)
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
