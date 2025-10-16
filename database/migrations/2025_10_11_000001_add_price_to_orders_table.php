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
            $table->decimal('total_price', 10, 2)->nullable()->after('status');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_price');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('discount_amount');
            $table->decimal('final_price', 10, 2)->nullable()->after('tax_amount');
            $table->string('currency', 10)->default('SAR')->after('final_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_price', 'discount_amount', 'tax_amount', 'final_price', 'currency']);
        });
    }
};

