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
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'clock_in_latitude')) {
                $table->decimal('clock_in_latitude', 10, 8)->nullable()->after('clock_in_location');
            }

            if (!Schema::hasColumn('attendances', 'clock_in_longitude')) {
                $table->decimal('clock_in_longitude', 11, 8)->nullable()->after('clock_in_latitude');
            }

            if (!Schema::hasColumn('attendances', 'clock_in_accuracy')) {
                $table->decimal('clock_in_accuracy', 8, 2)->nullable()->after('clock_in_longitude');
            }

            if (!Schema::hasColumn('attendances', 'clock_out_latitude')) {
                $table->decimal('clock_out_latitude', 10, 8)->nullable()->after('clock_out_location');
            }

            if (!Schema::hasColumn('attendances', 'clock_out_longitude')) {
                $table->decimal('clock_out_longitude', 11, 8)->nullable()->after('clock_out_latitude');
            }

            if (!Schema::hasColumn('attendances', 'clock_out_accuracy')) {
                $table->decimal('clock_out_accuracy', 8, 2)->nullable()->after('clock_out_longitude');
            }

            $table->index(['clock_in_latitude', 'clock_in_longitude'], 'attendance_location_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_latitude',
                'clock_in_longitude',
                'clock_in_accuracy',
                'clock_out_latitude',
                'clock_out_longitude',
                'clock_out_accuracy',
            ]);

            $table->dropIndex('attendance_location_index');
        });
    }
};
