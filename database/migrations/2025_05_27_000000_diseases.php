<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diseases', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 255);
            $table->string('category', 255)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('baseline_prevalence')->unsigned()->nullable()->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
