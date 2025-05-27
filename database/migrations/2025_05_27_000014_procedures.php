<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->integer('disease_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
