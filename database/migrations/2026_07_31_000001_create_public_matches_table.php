<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_matches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name_a', 100)->default('Pemain 1');
            $table->string('name_b', 100)->default('Pemain 2');
            $table->unsignedInteger('games_to_win')->default(2);
            $table->json('games_detail')->nullable();
            $table->unsignedInteger('score1')->nullable();
            $table->unsignedInteger('score2')->nullable();
            $table->enum('status', ['pending', 'ongoing', 'completed'])->default('ongoing');
            $table->unsignedTinyInteger('winner_side')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_matches');
    }
};
