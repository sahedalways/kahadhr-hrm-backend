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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Employee info
            $table->string('f_name')->nullable();
            $table->string('l_name')->nullable();
            $table->enum('title', ['Mr', 'Mrs'])->nullable();
            $table->string('email')->unique();
            $table->string('phone_no')->unique()->nullable();

            $table->string('avatar')->nullable();

            $table->boolean('is_active')->default(true);
            $table->enum('role', ['employee', 'teamLead'])->default('employee');

            $table->string('nationality');
            $table->string('share_code')->nullable();
            $table->enum('share_code_status', ['unavailable', 'pending', 'verified', 'expired'])
                ->default('unavailable');
            $table->date('date_of_birth');

            $table->string('job_title')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('contract_hours', 5, 2)->nullable();
            $table->enum('salary_type', ['hourly', 'monthly'])->default('hourly');
            $table->enum('employment_status', ['part-time', 'full-time'])->default('full-time');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('invite_token')->nullable()->unique();
            $table->timestamp('invite_token_expires_at')->nullable();
            $table->boolean('verified')->default(false);
            $table->date('billable_from')->nullable();


            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
