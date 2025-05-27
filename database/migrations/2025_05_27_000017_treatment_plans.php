<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_id');
            $table->string('thread_id', 255);
            $table->text('symptoms');
            $table->text('ques_data');
            $table->text('answers')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};
