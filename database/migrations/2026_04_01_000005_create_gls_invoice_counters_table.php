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
            $table->string('prefix', 10);
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamp('updated_at')->useCurrent();

            $table->unique(['prefix', 'year', 'month'], 'gls_inv_cnt_unq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_invoice_counters');
    }
};
