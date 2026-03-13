<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Порядок миграций gls_*:
// 1. gls_projects          ← эта (нет зависимостей)
// 2. gls_payment_charges
// 3. gls_payment_transactions
// 4. gls_payment_allocations
// 5. gls_lesson_additional
// 6. gls_invoice_documents
// 7. gls_salary_calculations

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_projects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();   // space_memory | indigo
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_projects');
    }
};
