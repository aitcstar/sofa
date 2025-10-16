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
        /*Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // reminder, follow_up, report, etc.
            $table->string('frequency'); // daily, weekly, monthly, once
            $table->datetime('scheduled_at');
            $table->datetime('last_run_at')->nullable();
            $table->datetime('next_run_at')->nullable();
            $table->json('parameters')->nullable();
            $table->string('status')->default('active'); // active, paused, completed, failed
            $table->text('description')->nullable();
            $table->integer('max_attempts')->default(3);
            $table->integer('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['status', 'next_run_at']);
            $table->index(['type', 'frequency']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
