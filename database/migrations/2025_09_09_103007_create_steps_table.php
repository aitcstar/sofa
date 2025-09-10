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
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable(); // رابط الصورة
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('desc_en');
            $table->text('desc_ar');
            $table->integer('order')->default(1); // لترتيب الخطوات
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
