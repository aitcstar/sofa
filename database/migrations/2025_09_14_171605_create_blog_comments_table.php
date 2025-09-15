<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // التحكم من الأدمن
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_comments');
    }
};
