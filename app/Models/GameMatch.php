<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    protected $table = 'game_matches';

    protected $fillable = [
        'tournament_id', 'round', 'match_number',
        'team1_id', 'team2_id',
        'score1', 'score2',
        'games_detail',
        'winner_team_id',
        'next_match_id', 'next_slot',
        'status', 'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'round' => 'integer',
            'match_number' => 'integer',
            'score1' => 'integer',
            'score2' => 'integer',
            'games_detail' => 'array',
            'status' => 'string',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    /**
     * Initialize or get games_detail for a new match.
     */
    public function initGames(): void
    {
        $this->games_detail = [['t1' => 0, 't2' => 0]];
        $this->score1 = 0;
        $this->score2 = 0;
        $this->save();
    }

    /**
     * Get the current game index (0-based).
     */
    public function currentGameIndex(): int
    {
        if (!$this->games_detail) return 0;
        // Find the first game that hasn't been won yet
        foreach ($this->games_detail as $i => $game) {
            if ($this->gameWinner($game['t1'], $game['t2']) === null) {
                return $i;
            }
        }
        // All games are done
        return count($this->games_detail) - 1;
    }

    /**
     * Get current game scores.
     */
    public function currentScores(): array
    {
        $detail = $this->games_detail ?? [['t1' => 0, 't2' => 0]];
        $idx = $this->currentGameIndex();
        $game = $detail[$idx] ?? ['t1' => 0, 't2' => 0];
        return [$game['t1'], $game['t2']];
    }

    /**
     * Total games won by each team.
     */
    public function gamesWon(): array
    {
        if (!$this->games_detail) return [0, 0];
        $w1 = 0; $w2 = 0;
        foreach ($this->games_detail as $i => $game) {
            $winner = $this->gameWinner($game['t1'], $game['t2']);
            if ($winner === 1) $w1++;
            if ($winner === 2) $w2++;
        }
        return [$w1, $w2];
    }

    /**
     * Check if a game is over and who won.
     * Returns 1 (team1), 2 (team2), or null (not over).
     */
    public function gameWinner(int $t1, int $t2): ?int
    {
        // Cap at 30: first to 30 wins
        if ($t1 >= 30 && $t2 < 30) return 1;
        if ($t2 >= 30 && $t1 < 30) return 2;

        // Normal win by 2, minimum 21
        if ($t1 >= 21 && $t1 - $t2 >= 2) return 1;
        if ($t2 >= 21 && $t2 - $t1 >= 2) return 2;

        return null;
    }

    /**
     * Check if the match is over and who won.
     * Returns 1 (team1), 2 (team2), or null.
     */
    public function matchWinner(?int $gamesToWin = null): ?int
    {
        [$w1, $w2] = $this->gamesWon();
        $target = $gamesToWin ?? 2;
        if ($w1 >= $target) return 1;
        if ($w2 >= $target) return 2;
        return null;
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class, 'next_match_id');
    }

    public function isTeam1Winner(): bool
    {
        return $this->winner_team_id && $this->winner_team_id === $this->team1_id;
    }

    public function isTeam2Winner(): bool
    {
        return $this->winner_team_id && $this->winner_team_id === $this->team2_id;
    }

    public function scoreDisplay(): string
    {
        if ($this->status === 'pending') {
            return '-';
        }
        return ($this->score1 ?? 0) . ' - ' . ($this->score2 ?? 0);
    }

    public function winnerName(): ?string
    {
        return $this->winner?->name;
    }

    /**
     * Apakah match ini adalah bye?
     * Bye terjadi saat jumlah tim ganjil — satu tim otomatis maju tanpa bertanding.
     */
    public function isBye(): bool
    {
        if ($this->status !== 'completed') return false;
        return is_null($this->team1_id) || is_null($this->team2_id);
    }
}
