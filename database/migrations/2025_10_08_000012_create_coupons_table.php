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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('value', 10, 2); // النسبة أو المبلغ الثابت
            $table->decimal('minimum_amount', 10, 2)->nullable(); // الحد الأدنى للطلب
            $table->decimal('maximum_discount', 10, 2)->nullable(); // الحد الأقصى للخصم
            $table->integer('usage_limit')->nullable(); // عدد مرات الاستخدام المسموح
            $table->integer('usage_limit_per_customer')->nullable(); // عدد مرات الاستخدام لكل عميل
            $table->integer('used_count')->default(0); // عدد مرات الاستخدام الفعلي
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_packages')->nullable(); // الباكجات المطبق عليها الكوبون
            $table->json('applicable_customers')->nullable(); // العملاء المطبق عليهم الكوبون
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index(['code', 'is_active']);
            $table->index(['starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
