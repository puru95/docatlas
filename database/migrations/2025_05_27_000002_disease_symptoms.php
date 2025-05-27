<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disease_symptoms', function (Blueprint $table) {
            $table->integer('disease_id');
            $table->integer('symptom_id');
            $table->tinyInteger('weight')->unsigned()->nullable()->default(75);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disease_symptoms');
    }
};
