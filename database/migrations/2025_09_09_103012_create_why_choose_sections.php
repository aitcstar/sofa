<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('why_choose_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('desc_en')->nullable();
            $table->text('desc_ar')->nullable();
            $table->timestamps();
        });

        Schema::create('why_choose_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('why_choose_section_id')->constrained()->cascadeOnDelete();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('desc_en')->nullable();
            $table->text('desc_ar')->nullable();
            $table->string('icon')->nullable(); // مسار الأيقونة
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('why_choose_items');
        Schema::dropIfExists('why_choose_sections');
    }
};
