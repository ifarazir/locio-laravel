<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diary_like', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diary_id');
            $table->foreign('diary_id')->references('id')->on('diaries')->cascadeOnDelete();
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_like');
    }
};
