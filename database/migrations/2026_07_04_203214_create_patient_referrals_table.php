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
        Schema::create('patient_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('referred_by')->constrained('doctors')->onDelete('cascade');
            
            // نوع التحويل
            $table->enum('type', ['internal', 'external']);
            
            // للتحويل الداخلي
            $table->foreignId('referred_to_doctor_id')
                  ->nullable()
                  ->constrained('doctors')
                  ->onDelete('set null');
            
            // للتحويل الخارجي
            $table->string('external_center_name')->nullable();
            $table->string('external_center_phone')->nullable();
            $table->string('external_center_address')->nullable();
            
            // معلومات عامة
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed'])
                  ->default('pending');
            $table->timestamp('referred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_referrals');
    }
};
