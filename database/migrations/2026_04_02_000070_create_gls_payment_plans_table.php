<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_payment_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedSmallInteger('months');
            $table->unsignedSmallInteger('lessons');
            $table->decimal('price', 10, 2);
            $table->char('currency', 3)->default('PLN');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('gls_projects')->onDelete('cascade');
            $table->unique(['project_id', 'months']);
            $table->index(['project_id', 'is_active']);
            $table->index(['project_id', 'sort_order']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('gls_payment_plans');
    }
};
