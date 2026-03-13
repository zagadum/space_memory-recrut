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
            if (!Schema::hasColumn('recruting_student', 'name')) {
                $table->string('name')->nullable()->after('email');
            }

            if (!Schema::hasColumn('recruting_student', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable()->after('country');
            }

            if (!Schema::hasColumn('recruting_student', 'locality')) {
                $table->string('locality')->nullable()->after('city');
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
                ['name', 'country_id', 'locality'],
                static fn (string $column): bool => Schema::hasColumn('recruting_student', $column)
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

