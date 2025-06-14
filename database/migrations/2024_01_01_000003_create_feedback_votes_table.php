<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feedback_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('feedback_id')->constrained('feedback')->onDelete('cascade');
            $table->boolean('is_upvote')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'feedback_id']);
            $table->index(['feedback_id', 'is_upvote']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback_votes');
    }
};
