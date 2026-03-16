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
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->text('hobbies')->nullable()->after('reg_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->dropColumn('hobbies');
        });
    }
};
