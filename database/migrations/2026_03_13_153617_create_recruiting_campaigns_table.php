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
        Schema::create('recruiting_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');                           // "Весенний набор 2026"
            $table->string('status')->default('draft')->index('idx_rc_status');       // draft | sending | paused | completed
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('clicked_count')->default(0);
            $table->unsignedInteger('converted_count')->default(0);
            $table->string('email_subject');
            $table->text('email_template');                   // Blade template name or inline
            $table->string('created_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruiting_campaigns');
    }
};
