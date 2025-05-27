<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disease_clinical_data', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('disease_id');
            $table->string('disease_name', 255);
            $table->string('category', 100)->nullable();
            $table->text('symptoms')->nullable();
            $table->text('lab_tests')->nullable();
            $table->text('procedures')->nullable();
            $table->text('medicines')->nullable();
            $table->text('salts')->nullable();
            $table->text('advice')->nullable();
            $table->string('follow_up', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_clinical_data');
    }
};
