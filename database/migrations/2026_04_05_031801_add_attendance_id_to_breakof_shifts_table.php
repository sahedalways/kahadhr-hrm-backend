<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('breakof_shifts', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->foreignId('attendance_id')->nullable()->after('shift_date_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_date_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('breakof_shifts', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->dropForeign(['attendance_id']);
            $table->dropColumn('attendance_id');
            $table->foreignId('shift_date_id')->nullable(false)->change();
        });
    }
};
