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
            $table->string('parent_name')->nullable();
            $table->string('parent_surname')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('parent_passport')->nullable();
            $table->date('dob')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('apartment')->nullable();
            $table->boolean('photo_consent')->default(0);
            $table->text('reg_comment')->nullable();
            $table->boolean('terms_accepted')->default(0);
            $table->boolean('privacy_accepted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruting_student', function (Blueprint $table) {
            $table->dropColumn([
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
                'privacy_accepted'
            ]);
        });
    }
};
