<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 255)->nullable();
            $table->string('salt', 255)->nullable();
            $table->string('category', 255)->nullable();
            $table->string('uses', 255)->nullable();
            $table->string('typical_dosage', 255)->nullable();
            $table->integer('disease_id')->nullable();
            $table->text('introduction')->nullable();
            $table->text('benefits')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('substitutes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
