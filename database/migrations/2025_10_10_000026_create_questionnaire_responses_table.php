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
        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable(); // For anonymous users
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('commercial_register')->nullable();
            $table->string('tax_number')->nullable();
            $table->json('responses'); // User's answers
            $table->decimal('calculated_price', 12, 2)->nullable();
            $table->json('price_breakdown')->nullable(); // Detailed price calculation
            $table->string('status')->default('pending'); // pending, contacted, converted
            $table->timestamp('contacted_at')->nullable();
            $table->foreignId('converted_lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['questionnaire_id', 'status']);
            $table->index(['email', 'questionnaire_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};
