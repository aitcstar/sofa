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
        Schema::create('order_timeline_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('order_timeline_sections')->onDelete('cascade');
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('desc_en');
            $table->text('desc_ar');
            $table->string('color')->nullable(); // للون الخلفية والدائرة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_timeline_sections');
    }
};
