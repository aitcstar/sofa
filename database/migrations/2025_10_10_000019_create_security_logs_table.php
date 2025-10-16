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
        /*Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // login_attempt, suspicious_activity, data_access, etc.
            $table->string('severity'); // low, medium, high, critical
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('action');
            $table->text('description');
            $table->json('metadata')->nullable(); // Additional data
            $table->string('status'); // success, failed, blocked
            $table->string('location')->nullable(); // Country/City
            $table->boolean('is_suspicious')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->datetime('occurred_at');
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['ip_address', 'occurred_at']);
            $table->index(['is_suspicious', 'severity']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
