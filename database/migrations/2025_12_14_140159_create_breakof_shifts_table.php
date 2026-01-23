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
        Schema::create('breakof_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->decimal('duration', 5, 2);
            $table->foreignId('shift_date_id')->constrained('shift_dates')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breakof_shifts');
    }
};