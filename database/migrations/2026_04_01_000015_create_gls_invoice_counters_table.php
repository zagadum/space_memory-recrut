<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_invoice_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('gls_projects')->onDelete('cascade');
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['project_id', 'period_year', 'period_month'], 'gls_inv_cnt_unq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_invoice_counters');
    }
};
