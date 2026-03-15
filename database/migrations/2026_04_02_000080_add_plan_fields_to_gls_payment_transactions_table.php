<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gls_payment_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_plan_id')->nullable()->after('project_id');
            $table->unsignedSmallInteger('months')->nullable()->after('currency');
            $table->unsignedSmallInteger('lessons')->nullable()->after('months');
            $table->string('title')->nullable()->after('lessons');
            $table->foreign('payment_plan_id')
                ->references('id')
                ->on('gls_payment_plans')
                ->nullOnDelete();
            $table->index(['payment_plan_id']);
        });
    }
    public function down(): void
    {
        Schema::table('gls_payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_plan_id']);
            $table->dropIndex(['payment_plan_id']);
            $table->dropColumn(['payment_plan_id', 'months', 'lessons', 'title']);
        });
    }
};
