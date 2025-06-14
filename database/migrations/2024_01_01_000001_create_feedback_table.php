<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['feature_request', 'improvement', 'question', 'other'])->default('feature_request');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->integer('votes')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['category', 'status']);
            $table->index('votes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback');
    }
};
