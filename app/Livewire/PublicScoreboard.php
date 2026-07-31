<?php

namespace App\Livewire;

use App\Models\PublicMatch;
use Livewire\Component;

class PublicScoreboard extends Component
{
    /** Satu scoreboard publik bersama — tanpa kode, tanpa intro. */
    public const PUBLIC_CODE = 'PUBLIC';

    public PublicMatch $match;
    public array $scores = [0, 0];
    public array $gamesWon = [0, 0];
    public int $currentGame = 0;
    public string $gameLabel = 'Game 1';
    public bool $matchOver = false;
    public ?int $matchWinner = null;
    public array $previousGames = [];
    public bool $showSwitchCourt = false;
    public bool $courtFlipped = false;
    public int $gamesToWin = 2;

    public function mount()
    {
        $this->match = PublicMatch::firstOrCreate(
            ['code' => self::PUBLIC_CODE],
            ['name_a' => 'Tim 1', 'name_b' => 'Tim 2', 'games_to_win' => 2]
        );

        if (!$this->match->games_detail) {
            $this->match->initGames();
        }

        $this->gamesToWin = $this->match->games_to_win;
        $this->refreshState();
    }

    public function resetBoard(): void
    {
        $this->match->name_a = 'Tim 1';
        $this->match->name_b = 'Tim 2';
        $this->match->games_to_win = 2;
        $this->match->status = 'ongoing';
        $this->match->winner_side = null;
        $this->match->finished_at = null;
        $this->match->initGames();

        $this->showSwitchCourt = false;
        $this->refreshState();
    }

    public function refreshState(): void
    {
        $this->match->refresh();
        $this->currentGame = $this->match->currentGameIndex();
        [$s1, $s2] = $this->match->currentScores();
        $this->scores = [$s1, $s2];
        [$w1, $w2] = $this->match->gamesWon();
        $this->gamesWon = [$w1, $w2];
        $this->gameLabel = 'Game ' . ($this->currentGame + 1);
        $this->courtFlipped = $this->currentGame % 2 == 1;
        $this->matchWinner = $this->match->matchWinner($this->gamesToWin);
        $this->matchOver = $this->matchWinner !== null;

        if (!$this->matchOver && !$this->showSwitchCourt) {
            $detail = $this->match->games_detail ?? [];
            $lastIdx = count($detail) - 1;
            if ($lastIdx >= 0 && $this->currentGame === $lastIdx) {
                $lastGame = $detail[$lastIdx];
                if ($this->match->gameWinner($lastGame['t1'], $lastGame['t2']) !== null) {
                    $this->showSwitchCourt = true;
                }
            }
        }

        $detail = $this->match->games_detail ?? [];
        $this->previousGames = [];
        foreach ($detail as $i => $game) {
            $winner = $this->match->gameWinner($game['t1'], $game['t2']);
            $this->previousGames[] = [
                'game' => $i + 1,
                'winner' => $winner,
                'score1' => $game['t1'],
                'score2' => $game['t2'],
            ];
        }
    }

    public function increment(int $side): void
    {
        if ($this->matchOver) return;
        if ($this->showSwitchCourt) return;
        if ($this->match->status !== 'ongoing') return;

        $detail = $this->match->games_detail;
        $idx = $this->currentGame;

        if (!isset($detail[$idx])) return;

        $detail[$idx][$side === 1 ? 't1' : 't2']++;

        $t1 = $detail[$idx]['t1'];
        $t2 = $detail[$idx]['t2'];
        $gameWinner = $this->match->gameWinner($t1, $t2);

        if ($gameWinner !== null) {
            [$w1, $w2] = $this->recalculateGamesWon($detail);
            $this->match->score1 = $w1;
            $this->match->score2 = $w2;
            $this->match->games_detail = $detail;

            $matchWinner = null;
            if ($w1 >= $this->gamesToWin) $matchWinner = 1;
            elseif ($w2 >= $this->gamesToWin) $matchWinner = 2;

            if ($matchWinner !== null) {
                $this->match->winner_side = $matchWinner;
                $this->match->status = 'completed';
                $this->match->finished_at = now();
                $this->match->save();
            } else {
                $this->match->save();
                $this->showSwitchCourt = true;
            }
        } else {
            $this->match->games_detail = $detail;
            $this->match->save();
        }
    }

    public function decrement(int $side): void
    {
        if ($this->matchOver) return;
        if ($this->showSwitchCourt) return;
        if ($this->match->status !== 'ongoing') return;

        $detail = $this->match->games_detail;
        $idx = $this->currentGame;

        if (!isset($detail[$idx])) return;

        $key = $side === 1 ? 't1' : 't2';
        if ($detail[$idx][$key] > 0) {
            $detail[$idx][$key]--;
        }

        $this->match->games_detail = $detail;
        $this->match->save();
    }

    public function confirmSwitchCourt(): void
    {
        $detail = $this->match->games_detail;
        $detail[] = ['t1' => 0, 't2' => 0];
        $this->match->games_detail = $detail;
        $this->match->save();
        $this->showSwitchCourt = false;
    }

    private function recalculateGamesWon(array $detail): array
    {
        $w1 = 0; $w2 = 0;
        foreach ($detail as $game) {
            $winner = $this->match->gameWinner($game['t1'], $game['t2']);
            if ($winner === 1) $w1++;
            if ($winner === 2) $w2++;
        }
        return [$w1, $w2];
    }

    public function render()
    {
        $this->refreshState();

        return view('livewire.public-scoreboard')
            ->layout('layouts.scoreboard');
    }
}
