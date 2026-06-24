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
        Schema::create('consultation_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId(
                'consultation_order_id'
            )
                ->constrained()
                ->cascadeOnDelete();

            $table->string('payment_id')->unique();

            $table->bigInteger('amount');

            $table->enum('status', [

                'P',
                'A',
                'F',
                'C',

            ])->default('P');

            $table->string('rrn')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->json('raw_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_payments');
    }
};
