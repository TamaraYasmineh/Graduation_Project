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
        Schema::create('patient_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('archived_by')->constrained('users')->onDelete('cascade');
            $table->enum('reason', [
                'recovered',           // شفاء
                'death',               // وفاة
                'follow_up_ended',     // إنهاء متابعة
                'final_transfer',      // تحويل نهائي
                'other'                // سبب آخر
            ]);
            $table->text('note')->nullable();
            $table->timestamp('archived_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_archives');
    }
};
