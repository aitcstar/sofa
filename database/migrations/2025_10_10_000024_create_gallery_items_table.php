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
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('gallery_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('type')->default('image'); // image, video, 360_view
            $table->json('images')->nullable(); // For multiple images
            $table->string('project_type')->nullable(); // building, compound, villa, etc.
            $table->integer('units_count')->nullable();
            $table->string('location')->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->year('completion_year')->nullable();
            $table->json('features')->nullable(); // Project features
            $table->json('colors')->nullable(); // Available colors
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'is_active', 'sort_order']);
            $table->index(['project_type', 'is_active']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
