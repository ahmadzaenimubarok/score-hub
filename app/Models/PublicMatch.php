<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicMatch extends Model
{
    protected $table = 'public_matches';

    protected $fillable = [
        'code', 'name_a', 'name_b', 'games_to_win',
        'games_detail', 'score1', 'score2',
        'status', 'winner_side', 'finished_at',
    ];

    protected $attributes = [
        'status' => 'ongoing',
        'games_to_win' => 2,
    ];

    protected function casts(): array
    {
        return [
            'games_to_win' => 'integer',
            'games_detail' => 'array',
            'score1' => 'integer',
            'score2' => 'integer',
            'status' => 'string',
            'finished_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PublicMatch $match) {
            if (empty($match->code)) {
                $match->code = strtoupper(Str::random(6));
            }
        });
    }

    /**
     * Sama seperti GameMatch::initGames() — alur scoring identik.
     */
    public function initGames(): void
    {
        $this->games_detail = [['t1' => 0, 't2' => 0]];
        $this->score1 = 0;
        $this->score2 = 0;
        $this->save();
    }

    public function currentGameIndex(): int
    {
        if (!$this->games_detail) return 0;
        foreach ($this->games_detail as $i => $game) {
            if ($this->gameWinner($game['t1'], $game['t2']) === null) {
                return $i;
            }
        }
        return count($this->games_detail) - 1;
    }

    public function currentScores(): array
    {
        $detail = $this->games_detail ?? [['t1' => 0, 't2' => 0]];
        $idx = $this->currentGameIndex();
        $game = $detail[$idx] ?? ['t1' => 0, 't2' => 0];
        return [$game['t1'], $game['t2']];
    }

    public function gamesWon(): array
    {
        if (!$this->games_detail) return [0, 0];
        $w1 = 0; $w2 = 0;
        foreach ($this->games_detail as $game) {
            $winner = $this->gameWinner($game['t1'], $game['t2']);
            if ($winner === 1) $w1++;
            if ($winner === 2) $w2++;
        }
        return [$w1, $w2];
    }

    public function gameWinner(int $t1, int $t2): ?int
    {
        if ($t1 >= 30 && $t2 < 30) return 1;
        if ($t2 >= 30 && $t1 < 30) return 2;
        if ($t1 >= 21 && $t1 - $t2 >= 2) return 1;
        if ($t2 >= 21 && $t2 - $t1 >= 2) return 2;
        return null;
    }

    public function matchWinner(?int $gamesToWin = null): ?int
    {
        [$w1, $w2] = $this->gamesWon();
        $target = $gamesToWin ?? 2;
        if ($w1 >= $target) return 1;
        if ($w2 >= $target) return 2;
        return null;
    }
}
