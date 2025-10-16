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
        /*Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // percentage, fixed_amount
            $table->decimal('value', 10, 2); // Percentage or fixed amount
            $table->decimal('minimum_order_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_user')->nullable();
            $table->integer('used_count')->default(0);
            $table->datetime('starts_at');
            $table->datetime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_products')->nullable(); // Product IDs
            $table->json('applicable_categories')->nullable(); // Category IDs
            $table->json('applicable_users')->nullable(); // User IDs
            $table->string('target_audience')->default('all'); // all, new_customers, existing_customers
            $table->timestamps();

            $table->index(['code', 'is_active']);
            $table->index(['starts_at', 'expires_at']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
