<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migrations', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('migration', 255);
            $table->integer('batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migrations');
    }
};
