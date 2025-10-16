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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('source')->default('website'); // website, phone, referral, social_media, advertisement
            $table->enum('status', ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation', 'won', 'lost'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->date('expected_close_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamps();

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index('next_follow_up_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
