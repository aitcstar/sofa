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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع الإشعار
            $table->morphs('notifiable'); // المستخدم المستهدف
            $table->json('data'); // بيانات الإشعار
            $table->timestamp('read_at')->nullable(); // تاريخ القراءة
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('channel', ['database', 'email', 'sms', 'whatsapp'])->default('database');
            $table->boolean('sent')->default(false); // تم الإرسال
            $table->timestamp('sent_at')->nullable(); // تاريخ الإرسال
            $table->json('metadata')->nullable(); // بيانات إضافية
            $table->timestamps();
            
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['type', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
