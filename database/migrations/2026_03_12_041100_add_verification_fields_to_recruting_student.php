<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->string('verification_code', 4)->nullable()->after('api_token');
            $table->timestamp('email_verified_at')->nullable()->after('verification_code');
        });
    }

    public function down(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'email_verified_at']);
        });
    }
};
