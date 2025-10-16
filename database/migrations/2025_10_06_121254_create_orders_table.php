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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->string('name')->nullable();
            $table->string('phone');
            $table->string('country_code');
            $table->string('email')->nullable();
            $table->integer('units_count')->default(1);
            $table->enum('project_type', ['large', 'medium', 'small']);
            $table->enum('current_stage', ['design', 'execution', 'operation']);
            $table->boolean('has_interior_design')->default(false);
            $table->boolean('needs_finishing_help')->default(false);
            $table->boolean('needs_color_help')->default(false);
            $table->boolean('has_diagrams')->default(false);
            $table->text('colors')->nullable(); // JSON or comma-separated
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // الموظف المعين
            $table->enum('status', [
                'pending',      // جديد
                'confirmed',    // مؤكد
                'processing',   // قيد التنفيذ
                'shipped',      // تم الشحن
                'delivered',    // تم التسليم
                'archived',     // مؤرشف
                'cancelled'     // ملغى
            ])->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
