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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم العميل
            $table->string('location')->nullable(); // المدينة / الدولة
            $table->string('image')->nullable(); // صورة العميل
            $table->text('message'); // التعليق
            $table->unsignedTinyInteger('rating')->default(5); // التقييم (من 5)
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
