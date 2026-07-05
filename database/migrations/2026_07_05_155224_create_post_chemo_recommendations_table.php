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
        Schema::create('post_chemo_recommendations', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->enum('type', ['recommendation', 'warning_symptom']); // توصية أو عرض تحذيري
            $table->integer('order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['type', 'is_active']); // لتسريع الاستعلامات

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_chemo_recommendations');
    }
};
