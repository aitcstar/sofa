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
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');   // اسم القسم بالعربي
            $table->string('name_en');   // اسم القسم بالإنجليزي
            $table->string('slug_ar')->unique(); // سلاگ عربي
            $table->string('slug_en')->unique(); // سلاگ إنجليزي
            $table->boolean('status')->default(1); // حالة التفعيل
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
