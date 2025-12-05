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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('course_name');
            $table->text('description')->nullable();
            $table->enum('content_type', ['video', 'file', 'text'])->default('text');
            $table->string('file_path')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('required_proof')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
