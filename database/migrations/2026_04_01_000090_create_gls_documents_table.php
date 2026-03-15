<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gls_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('project_id');
            $table->string('doc_no', 100)->nullable();
            $table->enum('doc_status', ['new', 'pedding', 'sign'])->default('new');
            $table->string('pdf_path', 500)->nullable();
            $table->dateTime('sign_date')->nullable();

            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('gls_projects')
                ->onDelete('restrict');

            $table->index(['student_id', 'doc_status']);
            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gls_documents');
    }
};

