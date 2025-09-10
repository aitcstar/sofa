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
        Schema::create('process_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('desc_ar')->nullable();
            $table->text('desc_en')->nullable();
            $table->text('button_text_en')->nullable();
            $table->text('button_text_ar')->nullable();
            $table->string('avatar')->nullable(); // صورة المستخدم
            $table->string('name')->nullable();   // الاسم
            $table->integer('units')->default(0); // عدد الوحدات
            $table->string('status')->nullable(); // الحالة
            $table->integer('progress')->default(0); // نسبة التقدم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_sections');
    }
};
