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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_id')->unique();
            $table->bigInteger('amount');
            $table->enum('status', [
                'P', // pending
                'A', // accepted
                'F', // failed
                'C'  // canceled
            ])->default('P');
            $table->string('rrn')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable(); // حفظ رد Paymera
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
