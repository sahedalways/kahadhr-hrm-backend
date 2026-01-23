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
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_groups');
    }
};
