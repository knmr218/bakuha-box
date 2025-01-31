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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('player_1')->nullable();
            $table->foreign('player_1')->references('id')->on('players')->onDelete('cascade');
            $table->string('player_2')->nullable();
            $table->foreign('player_2')->references('id')->on('players')->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->references('id')->on('games')->onDelete('cascade');
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
