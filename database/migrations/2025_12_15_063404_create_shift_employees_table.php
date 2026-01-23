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
        Schema::create('shift_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_date_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('cascade');
            $table->unique(['shift_date_id', 'employee_id'], 'unique_shift_date_employee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_employees');
    }
};
