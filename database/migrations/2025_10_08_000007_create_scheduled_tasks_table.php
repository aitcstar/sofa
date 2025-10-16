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
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المهمة
            $table->string('type'); // نوع المهمة
            $table->json('data'); // بيانات المهمة
            $table->datetime('scheduled_at'); // موعد التنفيذ
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('result')->nullable(); // نتيجة التنفيذ
            $table->integer('attempts')->default(0); // عدد المحاولات
            $table->integer('max_attempts')->default(3); // الحد الأقصى للمحاولات
            $table->timestamp('executed_at')->nullable(); // تاريخ التنفيذ
            $table->timestamp('next_retry_at')->nullable(); // موعد المحاولة التالية
            $table->timestamps();
            
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
