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
            $table->string('status')->default('new')->after('email');
            // Воронка: 'new' | 'registered' | 'paid' | 'transferred' | 'archived'

            $table->string('phone')->nullable()->after('status');
            $table->string('subject')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->dropColumn(['status', 'phone', 'subject']);
        });
    }
};
