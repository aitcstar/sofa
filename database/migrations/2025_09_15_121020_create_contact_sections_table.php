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
        Schema::create('contact_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('desc_ar')->nullable();
            $table->text('desc_en')->nullable();
            $table->string('main_showroom_ar')->nullable();
            $table->string('main_showroom_en')->nullable();
            $table->string('work_hours_ar')->nullable();
            $table->string('work_hours_en')->nullable();
            $table->string('cta_heading_ar')->nullable();
            $table->string('cta_heading_en')->nullable();
            $table->text('cta_text_ar')->nullable();
            $table->text('cta_text_en')->nullable();
            $table->string('city_ar')->nullable();
            $table->string('city_en')->nullable();
            $table->string('address_ar')->nullable();
            $table->string('address_en')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_sections');
    }
};
