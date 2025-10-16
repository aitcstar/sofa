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
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // المستخدم الذي قام بالإجراء
            $table->string('action'); // نوع الإجراء
            $table->text('description'); // وصف الإجراء
            $table->json('old_data')->nullable(); // البيانات القديمة
            $table->json('new_data')->nullable(); // البيانات الجديدة
            $table->string('ip_address')->nullable(); // عنوان IP
            $table->text('user_agent')->nullable(); // معلومات المتصفح
            $table->timestamps();
            
            $table->index(['order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_logs');
    }
};
