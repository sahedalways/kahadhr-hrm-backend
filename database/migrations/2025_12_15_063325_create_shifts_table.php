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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('job')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('address')->nullable();
            $table->text('note')->nullable();

            $table->foreignId('template_id')->nullable()->constrained('shift_templates')->onDelete('set null');
            $table->foreignId('break_id')->nullable()->constrained('shift_breaks')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
