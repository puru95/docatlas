<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 255)->nullable();
            $table->text('purpose')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
