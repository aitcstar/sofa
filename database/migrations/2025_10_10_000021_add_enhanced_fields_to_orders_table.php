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
        Schema::table('orders', function (Blueprint $table) {
            // Enhanced order tracking
            //$table->string('order_number')->unique()->after('id');
            //$table->string('priority')->default('medium')->after('status'); // low, medium, high, urgent
            //$table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->after('priority');
            //$table->text('internal_notes')->nullable()->after('notes');
            //$table->json('custom_fields')->nullable()->after('internal_notes');

            // Timeline and progress tracking
            $table->integer('progress_percentage')->default(0)->after('custom_fields');
            $table->datetime('confirmed_at')->nullable()->after('progress_percentage');
            $table->datetime('production_started_at')->nullable()->after('confirmed_at');
            $table->datetime('shipped_at')->nullable()->after('production_started_at');
            //$table->datetime('delivered_at')->nullable()->after('shipped_at');
            $table->datetime('archived_at')->nullable()->after('delivered_at');

            // Payment tracking
            $table->decimal('first_payment_amount', 10, 2)->nullable()->after('total_amount');
            $table->decimal('second_payment_amount', 10, 2)->nullable()->after('first_payment_amount');
            $table->datetime('first_payment_date')->nullable()->after('second_payment_amount');
            $table->datetime('second_payment_date')->nullable()->after('first_payment_date');
            //$table->string('payment_status')->default('pending')->after('second_payment_date'); // pending, partial, paid

            // Customer information enhancement
            //$table->string('commercial_register')->nullable()->after('phone');
            //$table->string('tax_number')->nullable()->after('commercial_register');
            $table->string('customer_type')->default('individual')->after('tax_number'); // individual, company

            // Duplicate detection
            $table->string('duplicate_hash')->nullable()->after('customer_type');
            //$table->boolean('is_duplicate')->default(false)->after('duplicate_hash');
            $table->foreignId('original_order_id')->nullable()->constrained('orders')->onDelete('set null')->after('is_duplicate');

            // Add indexes for better performance
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['payment_status', 'created_at']);
            $table->index(['duplicate_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['original_order_id']);
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['assigned_to', 'status']);
            $table->dropIndex(['payment_status', 'created_at']);
            $table->dropIndex(['duplicate_hash']);

            $table->dropColumn([
                'order_number', 'priority', 'assigned_to', 'internal_notes', 'custom_fields',
                'progress_percentage', 'confirmed_at', 'production_started_at', 'shipped_at',
                'delivered_at', 'archived_at', 'first_payment_amount', 'second_payment_amount',
                'first_payment_date', 'second_payment_date', 'payment_status',
                'commercial_register', 'tax_number', 'customer_type', 'duplicate_hash',
                'is_duplicate', 'original_order_id'
            ]);
        });
    }
};
