<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opened_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('open_date');
            $table->time('open_time');
            $table->decimal('opening_amount', 10, 2)->default(0);
            $table->decimal('expected_amount', 10, 2)->default(0);
            $table->decimal('counted_amount', 10, 2)->nullable();
            $table->decimal('difference_amount', 10, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('closing_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status']);
            $table->index(['open_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};