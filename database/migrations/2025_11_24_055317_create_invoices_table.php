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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            $table->decimal('employee_fee', 10, 2);
            $table->integer('total_employees_billed');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 8, 2);
            $table->decimal('vat', 10, 2)->default(0);
            $table->string('invoice_number')->unique();
            $table->string('currency', 3)->default('GBP');
            $table->enum('status', ['paid', 'pending', 'failed'])->default('pending');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
