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
       /* Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // order_new, order_status_changed, payment_received, etc.
            $table->morphs('notifiable'); // User or Admin
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data
            $table->string('channel')->default('database'); // database, email, sms, whatsapp
            $table->datetime('read_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->integer('priority')->default(1); // 1=low, 2=medium, 3=high, 4=urgent
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['type', 'status']);
            $table->index(['read_at', 'created_at']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
