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
        /*Schema::create('website_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('user_id')->nullable();
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('device_type'); // desktop, mobile, tablet
            $table->string('browser');
            $table->string('operating_system');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('referrer_url')->nullable();
            $table->string('referrer_domain')->nullable();
            $table->string('landing_page');
            $table->string('exit_page')->nullable();
            $table->integer('page_views')->default(1);
            $table->integer('session_duration')->default(0); // in seconds
            $table->boolean('is_bounce')->default(false);
            $table->boolean('is_conversion')->default(false);
            $table->string('conversion_type')->nullable(); // order, quote_request, contact
            $table->decimal('conversion_value', 10, 2)->nullable();
            $table->datetime('session_start');
            $table->datetime('session_end')->nullable();
            $table->timestamps();

            $table->index(['session_start', 'device_type']);
            $table->index(['referrer_domain', 'session_start']);
            $table->index(['is_conversion', 'conversion_type']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_analytics');
    }
};
