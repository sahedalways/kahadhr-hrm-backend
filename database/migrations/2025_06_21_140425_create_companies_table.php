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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('sub_domain')->default('company');
            $table->string('company_house_number');
            $table->string('company_mobile');
            $table->string('company_email');
            $table->string('business_type')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('registered_domain')->nullable();
            $table->foreignId('billing_plan_id')
                ->nullable()
                ->constrained('billing_plans')
                ->onDelete('set null');
            $table->enum('subscription_status', ['active', 'trial', 'expired', 'suspended'])
                ->default('trial');
            $table->integer('payment_failed_count')->default(0);
            $table->enum('payment_status', ['paid', 'unpaid', 'pending'])->default('pending');
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            $table->date('trial_ends_at')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
