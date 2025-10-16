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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القالب
            $table->string('type'); // نوع الإشعار
            $table->string('subject'); // موضوع الإشعار
            $table->text('content'); // محتوى الإشعار
            $table->json('variables')->nullable(); // المتغيرات المتاحة
            $table->enum('channel', ['email', 'sms', 'whatsapp', 'database'])->default('email');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // إعدادات إضافية
            $table->timestamps();
            
            $table->unique(['type', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
