<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_service_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('professional_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('cash_register_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('service_date');
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('professional_percentage', 5, 2);
            $table->decimal('professional_amount', 10, 2);
            $table->decimal('salon_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->enum('payment_method', ['cash', 'pix', 'card', 'mixed'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['service_date']);
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_service_entries');
    }
};