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
            // إضافة حقول جديدة لإدارة الطلبات المحسنة
            $table->text('internal_notes')->nullable()->after('colors'); // ملاحظات داخلية
            $table->json('timeline_data')->nullable()->after('internal_notes'); // بيانات الجدول الزمني
            $table->decimal('total_amount', 10, 2)->nullable()->after('timeline_data'); // المبلغ الإجمالي
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount'); // المبلغ المدفوع
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid')->after('paid_amount'); // حالة الدفع
            $table->json('payment_schedule')->nullable()->after('payment_status'); // جدولة الدفعات
            $table->integer('priority')->default(1)->after('payment_schedule'); // أولوية الطلب (1-5)
            $table->timestamp('expected_delivery_date')->nullable()->after('priority'); // تاريخ التسليم المتوقع
            $table->json('custom_fields')->nullable()->after('expected_delivery_date'); // حقول مخصصة
            $table->boolean('is_duplicate')->default(false)->after('custom_fields'); // طلب مكرر
            $table->foreignId('duplicate_of')->nullable()->constrained('orders')->onDelete('set null')->after('is_duplicate'); // مرجع الطلب الأصلي
            $table->timestamp('last_activity_at')->nullable()->after('duplicate_of'); // آخر نشاط
            $table->json('activity_log')->nullable()->after('last_activity_at'); // سجل الأنشطة
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'internal_notes',
                'timeline_data',
                'total_amount',
                'paid_amount',
                'payment_status',
                'payment_schedule',
                'priority',
                'expected_delivery_date',
                'custom_fields',
                'is_duplicate',
                'duplicate_of',
                'last_activity_at',
                'activity_log'
            ]);
        });
    }
};
