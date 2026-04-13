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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();

         
            $table->foreignId('patient_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

           
            $table->text('chronic_diseases')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('notes')->nullable();

            
            $table->boolean('is_smoker')->default(false);
            $table->float('height')->nullable(); 
            $table->float('weight')->nullable(); 
            $table->string('blood_type')->nullable();

            
            $table->text('surgeries')->nullable();
            $table->text('family_history')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
