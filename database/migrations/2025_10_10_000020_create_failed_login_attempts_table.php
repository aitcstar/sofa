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
        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->integer('attempts_count')->default(1);
            $table->datetime('attempted_at');
            $table->datetime('first_attempt_at');
            $table->datetime('last_attempt_at');
            $table->datetime('blocked_until')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();
            
            $table->index(['email', 'ip_address']);
            $table->index(['ip_address', 'blocked_until']);
            $table->index('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_login_attempts');
    }
};
