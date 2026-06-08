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
        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            // معلومات أساسية
            $table->string('name'); // اسم البروتوكول
            $table->string('disease_type'); // نوع السرطان
            $table->string('therapeutic_intent'); // curative / palliative / adjuvant

            // الجدولة
            $table->integer('cycle_length_days'); // طول الدورة
            $table->text('administration_days'); // أيام الإعطاء
            $table->integer('suggested_number_of_cycles')->nullable();

            // التحضيرات
            $table->text('pre_medications')->nullable();
            $table->text('mandatory_tests')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protocols');
    }
};
