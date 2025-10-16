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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event_type'); // login, logout, failed_login, password_change, etc.
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('location')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->text('description');
            $table->json('metadata')->nullable(); // معلومات إضافية
            $table->boolean('is_suspicious')->default(false);
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['user_id', 'event_type']);
            $table->index(['ip_address', 'occurred_at']);
            $table->index(['risk_level', 'is_suspicious']);
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
