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
        Schema::create('order_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('stage'); // design, manufacturing, shipping, first_payment, second_payment
            $table->string('status')->default('pending'); // pending, in_progress, completed, delayed
            $table->datetime('planned_start_date')->nullable();
            $table->datetime('actual_start_date')->nullable();
            $table->datetime('planned_end_date')->nullable();
            $table->datetime('actual_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // File paths
            $table->integer('progress_percentage')->default(0);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['order_id', 'stage']);
            $table->index(['status', 'planned_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_timeline');
    }
};
