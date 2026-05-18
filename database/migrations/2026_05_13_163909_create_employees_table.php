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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->date('date_of_birth')->nullable();
            $table->enum('role', ['nurse', 'sanitation_worker']);
            $table->string('phone2')->nullable();
            $table->string('academic_degree')->nullable();
            $table->string('degree_image')->nullable();  // للممرض فقط
            $table->text('work_history')->nullable();
            $table->text('chronic_diseases')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->string('bank_account')->nullable();
            $table->string('sham_cash_number')->nullable();
            $table->decimal('salary', 10, 2);
            $table->enum('shift', ['morning', 'evening'])->default('morning');
            $table->json('work_days')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
