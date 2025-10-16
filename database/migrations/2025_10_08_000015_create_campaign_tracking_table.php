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
        Schema::create('campaign_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('action', ['sent', 'delivered', 'opened', 'clicked', 'unsubscribed', 'converted']);
            $table->string('tracking_id')->unique();
            $table->json('metadata')->nullable(); // معلومات إضافية مثل IP، User Agent، إلخ
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('marketing_campaigns')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['campaign_id', 'action']);
            $table->index(['user_id', 'action']);
            $table->index('tracking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_tracking');
    }
};
