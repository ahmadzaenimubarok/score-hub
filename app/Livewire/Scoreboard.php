<?php

namespace App\Livewire;

use App\Models\GameMatch;
use Livewire\Component;

class Scoreboard extends Component
{
    public GameMatch $match;
    public array $scores = [0, 0];
    public array $gamesWon = [0, 0];
    public int $currentGame = 0;
    public string $gameLabel = 'Game 1';
    public bool $matchOver = false;
    public ?int $matchWinner = null;
    public bool $readonly = false;
    public string $tournamentCode = '';
    public string $tournamentName = '';
    public int $gamesToWin = 2;

    public function mount(GameMatch $gameMatch)
    {
        $this->match = $gameMatch->load(['team1.members', 'team2.members', 'tournament']);

        // Simpan tournament info sebagai properti terpisah (reliable di Livewire)
        $this->tournamentCode = $this->match->tournament?->code ?? '';
        $this->tournamentName = $this->match->tournament?->name ?? 'Badminton Fun Match';
        $this->gamesToWin = $this->match->tournament?->games_to_win ?? 2;

        // Public scoreboard route → read-only
        if (request()->route()->named('public.scoreboard')) {
            $this->readonly = true;
        }

        if (!$this->match->games_detail) {
            $this->match->initGames();
        }

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
        $this->matchWinner = $this->match->matchWinner($this->gamesToWin);
        $this->matchOver = $this->matchWinner !== null;
    }

    /**
     * Click on team area = increment score.
     */
    public function increment(int $team): void
    {
        if ($this->readonly) return;
        if ($this->match->status !== 'ongoing') return;
        if ($this->matchOver) return;

        $detail = $this->match->games_detail;
        $idx = $this->currentGame;

        if (!isset($detail[$idx])) return;

        $detail[$idx][$team === 1 ? 't1' : 't2']++;

        // Check if game is over
        $t1 = $detail[$idx]['t1'];
        $t2 = $detail[$idx]['t2'];
        $gameWinner = $this->match->gameWinner($t1, $t2);

        if ($gameWinner !== null) {
            // Game over — check if match is also over
            [$w1, $w2] = $this->recalculateGamesWon($detail);
            $this->match->score1 = $w1;
            $this->match->score2 = $w2;
            $this->match->games_detail = $detail;

            $matchWinner = null;
            if ($w1 >= $this->gamesToWin) $matchWinner = 1;
            elseif ($w2 >= $this->gamesToWin) $matchWinner = 2;

            if ($matchWinner !== null) {
                // Match over!
                $winnerId = $matchWinner === 1 ? $this->match->team1_id : $this->match->team2_id;
                $this->match->winner_team_id = $winnerId;
                $this->match->status = 'completed';
                $this->match->finished_at = now();
                $this->match->save();

                // Advance winner to next match
                $this->advanceWinner($winnerId);
            } else {
                // Start next game
                $detail[] = ['t1' => 0, 't2' => 0];
                $this->match->games_detail = $detail;
                $this->match->save();
            }
        } else {
            $this->match->games_detail = $detail;
            $this->match->save();
        }

        $this->refreshState();
    }

    /**
     * Long-press on team area = decrement score (min 0).
     */
    public function decrement(int $team): void
    {
        if ($this->readonly) return;
        if ($this->match->status !== 'ongoing') return;
        if ($this->matchOver) return;

        $detail = $this->match->games_detail;
        $idx = $this->currentGame;

        if (!isset($detail[$idx])) return;

        $key = $team === 1 ? 't1' : 't2';
        if ($detail[$idx][$key] > 0) {
            $detail[$idx][$key]--;
        }

        $this->match->games_detail = $detail;
        $this->match->save();
        $this->refreshState();
    }

    /**
     * Close the scoreboard — return to bracket.
     */
    public function close()
    {
        // Reload tournament karena relationship tidak diserialisasi Livewire
        $this->match->load('tournament');
        $code = $this->match->tournament?->code;
        $tournamentId = $this->match->tournament_id;

        if ($this->readonly && $code) {
            $this->redirect('/t/' . $code);
        } else {
            $this->redirect('/admin/tournaments/' . $tournamentId);
        }
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

    private function advanceWinner(int $winnerTeamId): void
    {
        if ($this->match->next_match_id) {
            $nextMatch = GameMatch::find($this->match->next_match_id);
            if ($nextMatch) {
                $column = 'team' . $this->match->next_slot . '_id';
                $nextMatch->$column = $winnerTeamId;
                $nextMatch->save();
            }
        } else {
            // Final match selesai
            $this->match->tournament->update(['status' => 'completed']);
        }
    }

    public function render()
    {
        $this->refreshState();

        $closeUrl = $this->readonly
            ? ($this->tournamentCode ? '/t/' . $this->tournamentCode : '')
            : '/admin/tournaments/' . $this->match->tournament_id;

        return view('livewire.scoreboard', ['closeUrl' => $closeUrl])
            ->layout('layouts.scoreboard', ['closeUrl' => $closeUrl]);
    }
}
