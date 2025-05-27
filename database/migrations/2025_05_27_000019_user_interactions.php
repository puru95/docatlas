<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_interactions', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->integer('user_id');
            $table->string('thread_id', 255);
            $table->integer('sequence_no');
            $table->string('question', 255);
            $table->text('options');
            $table->string('answers', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_interactions');
    }
};
