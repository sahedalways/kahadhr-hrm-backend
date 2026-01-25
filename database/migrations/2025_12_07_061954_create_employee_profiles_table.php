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
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emp_id')->nullable()->constrained('employees')->cascadeOnDelete();

            $table->date('date_of_birth')->nullable();
            $table->string('street_1')->nullable();
            $table->string('street_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('nationality')->nullable();

            $table->string('home_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('personal_email')->nullable();

            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('tax_reference_number')->nullable();

            // Visa / Immigration / BRP
            $table->string('immigration_status')->nullable();
            $table->string('brp_number')->nullable();
            $table->date('brp_expiry_date')->nullable();
            $table->date('right_to_work_expiry_date')->nullable();

            // Passport
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
