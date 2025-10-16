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
        /*Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('source'); // website, phone, referral, social_media, etc.
            $table->string('status')->default('new'); // new, contacted, interested, not_interested, converted
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->text('project_description')->nullable();
            $table->string('project_type')->nullable(); // apartment, compound, hotel_apartments, villa
            $table->integer('units_count')->nullable();
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->datetime('best_contact_time')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('last_contact_at')->nullable();
            $table->datetime('next_follow_up_at')->nullable();
            $table->datetime('converted_at')->nullable();
            $table->foreignId('converted_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->index(['status', 'assigned_to']);
            $table->index(['next_follow_up_at', 'status']);
            $table->index(['source', 'created_at']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
