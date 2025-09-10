<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadyToFurnishSections extends Migration
{
    public function up()
    {
        Schema::create('ready_to_furnish_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('desc_en')->nullable();
            $table->text('desc_ar')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('start_order_link')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ready_to_furnish_sections');
    }
}
