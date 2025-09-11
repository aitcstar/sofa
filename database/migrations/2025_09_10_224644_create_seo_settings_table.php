<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('seo_settings', function (Blueprint $table) {
        $table->id();
        $table->string('page')->unique(); // home, blog, category, about ...

        // AR
        $table->string('meta_title_ar')->nullable();
        $table->text('meta_description_ar')->nullable();
        $table->string('slug_ar')->nullable();
        $table->string('canonical_ar')->nullable();

        // EN
        $table->string('meta_title_en')->nullable();
        $table->text('meta_description_en')->nullable();
        $table->string('slug_en')->nullable();
        $table->string('canonical_en')->nullable();

        $table->enum('index_status', ['index', 'noindex'])->default('index');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
