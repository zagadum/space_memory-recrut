л<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gls_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('gls_documents', 'doc_type')) {
                $table->string('doc_type', 50)->default('contract')->after('doc_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gls_documents', function (Blueprint $table) {
            if (Schema::hasColumn('gls_documents', 'doc_type')) {
                $table->dropColumn('doc_type');
            }
        });
    }
};

