<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gls_invoice_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('gls_invoice_documents', 'sale_date')) {
                $table->date('sale_date')->nullable()->after('issue_date');
            }
            if (!Schema::hasColumn('gls_invoice_documents', 'pdf_path')) {
                $table->string('pdf_path', 500)->nullable()->after('ksef_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gls_invoice_documents', function (Blueprint $table) {
            $table->dropColumn(['sale_date', 'pdf_path']);
        });
    }
};
