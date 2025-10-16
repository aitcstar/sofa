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
        Schema::table('users', function (Blueprint $table) {
            // Enhanced user information
            //$table->string('phone')->nullable()->after('email');
            $table->string('company')->nullable()->after('phone');
            $table->string('job_title')->nullable()->after('company');
            $table->text('address')->nullable()->after('job_title');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->default('Saudi Arabia')->after('city');

            // Customer type and classification
            $table->string('customer_type')->default('individual')->after('country'); // individual, company
            $table->string('customer_segment')->default('regular')->after('customer_type'); // regular, vip, premium
            $table->string('commercial_register')->nullable()->after('customer_segment');
            $table->string('tax_number')->nullable()->after('commercial_register');

            // Preferences and settings
            $table->string('preferred_language')->default('ar')->after('tax_number');
            $table->string('preferred_contact_method')->default('email')->after('preferred_language'); // email, phone, whatsapp
            $table->json('notification_preferences')->nullable()->after('preferred_contact_method');

            // Marketing and analytics
            $table->string('source')->nullable()->after('notification_preferences'); // website, referral, social_media, etc.
            $table->datetime('last_login_at')->nullable()->after('source');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->integer('total_orders')->default(0)->after('last_login_ip');
            $table->decimal('total_spent', 12, 2)->default(0)->after('total_orders');
            $table->decimal('average_order_value', 10, 2)->default(0)->after('total_spent');

            // Status and verification
            $table->boolean('is_verified')->default(false)->after('average_order_value');
            $table->datetime('verified_at')->nullable()->after('is_verified');
            //$table->boolean('is_active')->default(true)->after('verified_at');
            $table->datetime('deactivated_at')->nullable()->after('is_active');
            $table->text('deactivation_reason')->nullable()->after('deactivated_at');

            // Add indexes for better performance
            $table->index(['customer_type', 'customer_segment']);
            $table->index(['is_active', 'created_at']);
            $table->index(['total_spent', 'total_orders']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['customer_type', 'customer_segment']);
            $table->dropIndex(['is_active', 'created_at']);
            $table->dropIndex(['total_spent', 'total_orders']);

            $table->dropColumn([
                'phone', 'company', 'job_title', 'address', 'city', 'country',
                'customer_type', 'customer_segment', 'commercial_register', 'tax_number',
                'preferred_language', 'preferred_contact_method', 'notification_preferences',
                'source', 'last_login_at', 'last_login_ip', 'total_orders', 'total_spent',
                'average_order_value', 'is_verified', 'verified_at', 'is_active',
                'deactivated_at', 'deactivation_reason'
            ]);
        });
    }
};
