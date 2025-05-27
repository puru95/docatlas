<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptom_map', function (Blueprint $table) {
            $table->integer('id');
            $table->string('symptom', 255)->nullable();
            $table->string('disease', 255)->nullable();
            $table->integer('score')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_map');
    }
};
