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
        Schema::create('treatment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')
                ->constrained('treatment_plans')
                ->cascadeOnDelete();

            $table->date('session_date');

            // vitals
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->float('bsa')->nullable();

            // treatment
            $table->float('dosage')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('lab_requested')->default(false);

            $table->text('lab_tests_requested')->nullable();

            $table->text('lab_results')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_sessions');
    }
};
