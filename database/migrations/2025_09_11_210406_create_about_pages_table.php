<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('section'); // مثال: vision, values, why_sofa
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('text_ar')->nullable();
            $table->text('text_en')->nullable();
            $table->json('items_ar')->nullable();
            $table->json('items_en')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};
