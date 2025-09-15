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
    Schema::table('faqs', function (Blueprint $table) {
        $table->foreignId('blog_id')->nullable()->constrained('blogs')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('faqs', function (Blueprint $table) {
        $table->dropForeign(['blog_id']);
        $table->dropColumn('blog_id');
    });
}

};
