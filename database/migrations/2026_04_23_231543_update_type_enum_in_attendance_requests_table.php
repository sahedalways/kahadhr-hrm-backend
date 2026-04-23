<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE attendance_requests
            MODIFY type ENUM(
                'late_clock_in',
                'early_clock_out',
                'late_clock_out',
                'auto_clock_out'
            )
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE attendance_requests
            MODIFY type ENUM(
                'late_clock_in',
                'early_clock_out',
                'late_clock_out'
            )
        ");
    }
};
