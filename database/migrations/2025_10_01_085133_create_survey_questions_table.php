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
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar'); // مثال: "نوع العميل"
            $table->string('title_en');
            $table->enum('type', ['radio', 'checkbox', 'select', 'text', 'number']); // نوع الحقل
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0); // لترتيب الأسئلة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
