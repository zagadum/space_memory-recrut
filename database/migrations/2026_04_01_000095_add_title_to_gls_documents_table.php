<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gls_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('gls_documents', 'title')) {
                $table->string('title', 255)->nullable()->after('doc_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gls_documents', function (Blueprint $table) {
            if (Schema::hasColumn('gls_documents', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};

