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
        /*Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // call, email, meeting, note, quote_sent, etc.
            $table->string('subject')->nullable();
            $table->text('description');
            $table->datetime('activity_date');
            $table->integer('duration_minutes')->nullable();
            $table->string('outcome')->nullable(); // positive, negative, neutral, follow_up_needed
            $table->datetime('next_action_date')->nullable();
            $table->string('next_action_type')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'activity_date']);
            $table->index(['type', 'activity_date']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
