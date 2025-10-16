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
        /*Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->string('payment_method'); // cash, bank_transfer, credit_card, check
            $table->string('payment_type')->default('full'); // full, first_payment, second_payment
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->string('reference_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->datetime('payment_date');
            $table->datetime('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional payment data
            $table->string('receipt_path')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'payment_type']);
            $table->index(['status', 'payment_date']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
