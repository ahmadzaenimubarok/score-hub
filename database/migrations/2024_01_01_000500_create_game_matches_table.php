<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('round');
            $table->unsignedInteger('match_number');
            $table->foreignId('team1_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('team2_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->unsignedInteger('score1')->nullable();
            $table->unsignedInteger('score2')->nullable();
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('next_match_id')->nullable()->constrained('game_matches')->nullOnDelete();
            $table->enum('next_slot', [1, 2])->nullable();
            $table->enum('status', ['pending', 'ongoing', 'completed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['tournament_id', 'round', 'match_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
