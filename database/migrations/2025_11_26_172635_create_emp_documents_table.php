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
        Schema::create('emp_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Document Type
            $table->foreignId('doc_type_id')
                ->constrained('document_types')
                ->cascadeOnDelete();

            // Employee
            $table->foreignId('emp_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            // Company
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('file_path')->nullable();

            $table->date('expires_at')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_documents');
    }
};
