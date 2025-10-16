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
        /*Schema::create('campaign_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->onDelete('cascade');
            $table->morphs('recipient'); // User or Lead
            $table->string('status'); // sent, delivered, opened, clicked, bounced, unsubscribed
            $table->datetime('sent_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            $table->datetime('opened_at')->nullable();
            $table->datetime('clicked_at')->nullable();
            $table->datetime('bounced_at')->nullable();
            $table->datetime('unsubscribed_at')->nullable();
            $table->string('bounce_reason')->nullable();
            $table->string('clicked_url')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index(['recipient_type', 'recipient_id']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_tracking');
    }
};
