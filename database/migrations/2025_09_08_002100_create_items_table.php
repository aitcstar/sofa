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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('design_id')->constrained()->onDelete('cascade');
            $table->string('item_name_ar'); // "كنبة 3 مقاعد"
            $table->string('item_name_en');
            $table->integer('quantity');
            $table->string('dimensions')->nullable(); // "2.2x100x70"
            $table->string('material'); // "قماش مخملي", "جلد"
            $table->string('color'); // "أسود", "بني"
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
