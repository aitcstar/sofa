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
        Schema::create('order_stages', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar'); // عنوان المرحلة بالعربية
            $table->string('title_en')->nullable(); // عنوان المرحلة بالإنجليزية (اختياري)
            $table->json('description_ar')->nullable(); // وصف المرحلة بالعربية (array)
            $table->json('description_en')->nullable(); // وصف المرحلة بالإنجليزية (array)
            $table->integer('order_number')->default(1); // ترتيب المرحلة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_stages');
    }
};
