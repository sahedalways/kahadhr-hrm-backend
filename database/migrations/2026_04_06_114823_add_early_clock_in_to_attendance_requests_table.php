<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL 8.0+ or MariaDB 10.2.1+
        DB::statement("ALTER TABLE attendance_requests MODIFY COLUMN type ENUM('late_clock_in', 'early_clock_out', 'late_clock_out', 'early_clock_in') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values (without early_clock_in)
        DB::statement("ALTER TABLE attendance_requests MODIFY COLUMN type ENUM('late_clock_in', 'early_clock_out', 'late_clock_out') NOT NULL");
    }
};
