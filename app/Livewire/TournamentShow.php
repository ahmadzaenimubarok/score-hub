<?php

namespace App\Livewire;

use App\Livewire\Concerns\ComputesBracketLayout;
use App\Models\GameMatch;
use App\Models\Participant;
use App\Models\Team;
use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

#[Layout('layouts.app')]
class TournamentShow extends Component
{
    use WithPagination;
    use ComputesBracketLayout;

    public Tournament $tournament;
    public string $activeTab = 'participants';

    // Participant management
    public string $participantName = '';

    // Score update
    public ?int $updatingMatchId = null;
    public ?int $score1 = null;
    public ?int $score2 = null;

    // Estimasi waktu
    public int $estimateCourts = 1;

    #[Url(as: 'tab')]
    public string $tab = 'participants';

    public function mount(Tournament $tournament)
    {
        $this->tournament = $tournament->load(['participants', 'teams.members', 'gameMatches.team1', 'gameMatches.team2', 'gameMatches.winner']);
    }

    // ========== PARTICIPANTS ==========

    public function addParticipant()
    {
        if (! $this->ensureStatus('Peserta hanya bisa ditambahkan saat status draft.', Tournament::STATUS_DRAFT)) {
            return;
        }

        $this->validate(['participantName' => 'required|string|max:255']);

        $this->tournament->participants()->create([
            'name' => $this->participantName,
        ]);

        $this->participantName = '';
        $this->tournament->load('participants');
    }

    public function removeParticipant($id)
    {
        if (! $this->ensureStatus('Peserta hanya bisa dihapus saat status draft.', Tournament::STATUS_DRAFT)) {
            return;
        }

        $this->tournament->participants()->findOrFail($id)->delete();
        $this->tournament->load('participants');
    }

    // ========== TEAMS ==========

    public function generateTeams()
    {
        if (! $this->ensureStatus('Generate tim hanya bisa dilakukan saat status draft.', Tournament::STATUS_DRAFT)) {
            return;
        }

        $participants = $this->tournament->participants;

        if ($participants->count() < 2) {
            session()->flash('error', 'Minimal 2 peserta untuk membuat tim.');
            return;
        }

        // Hapus tim yang sudah ada
        $this->tournament->teams()->delete();

        $shuffled = $participants->shuffle();
        $teamNumber = 1;

        for ($i = 0; $i < $shuffled->count(); $i += 2) {
            $team = $this->tournament->teams()->create([
                'name' => 'Tim ' . $teamNumber,
            ]);

            $team->members()->attach($shuffled[$i]->id);

            if (isset($shuffled[$i + 1])) {
                $team->members()->attach($shuffled[$i + 1]->id);
            }

            $teamNumber++;
        }

        $this->tournament->load('teams.members');
        session()->flash('message', 'Tim berhasil digenerate!');
    }

    // ========== BRACKET ==========

    public function generateBracket()
    {
        if (! $this->ensureStatus('Generate bracket hanya bisa dilakukan saat status draft.', Tournament::STATUS_DRAFT)) {
            return;
        }

        $teams = $this->tournament->teams;

        if ($teams->count() < 2) {
            session()->flash('error', 'Minimal 2 tim untuk membuat bracket.');
            return;
        }

        // Hapus pertandingan yang sudah ada
        $this->tournament->gameMatches()->delete();

        $teamIds = $teams->pluck('id')->shuffle()->values();
        $totalTeams = $teamIds->count();
        $totalRounds = (int) ceil(log($totalTeams, 2));
        $totalSlots = (int) pow(2, $totalRounds);

        // Buat placeholder matches untuk setiap ronde
        $matchesByRound = [];

        for ($round = 1; $round <= $totalRounds; $round++) {
            $matchesInRound = (int) pow(2, $totalRounds - $round);
            $matchesByRound[$round] = [];

            for ($m = 1; $m <= $matchesInRound; $m++) {
                $match = $this->tournament->gameMatches()->create([
                    'round' => $round,
                    'match_number' => $m,
                    'status' => 'pending',
                ]);

                $matchesByRound[$round][] = $match;
            }
        }

        // Isi tim ke ronde 1
        for ($i = 0; $i < $totalSlots; $i++) {
            $matchIndex = (int) floor($i / 2);
            $slot = ($i % 2) + 1;

            if ($matchIndex < count($matchesByRound[1])) {
                $match = $matchesByRound[1][$matchIndex];
                $column = 'team' . $slot . '_id';
                $match->$column = $teamIds[$i] ?? null;
                $match->save();
            }
        }

        // Hubungkan next_match_id
        for ($round = 1; $round < $totalRounds; $round++) {
            $matchesInRound = count($matchesByRound[$round]);

            for ($m = 0; $m < $matchesInRound; $m++) {
                $nextMatchIndex = (int) floor($m / 2);
                $nextSlot = ($m % 2) + 1;

                if (isset($matchesByRound[$round + 1][$nextMatchIndex])) {
                    $match = $matchesByRound[$round][$m];
                    $match->next_match_id = $matchesByRound[$round + 1][$nextMatchIndex]->id;
                    $match->next_slot = $nextSlot;
                    $match->save();
                }
            }
        }

        $this->tournament->load('gameMatches.team1', 'gameMatches.team2');
        $this->advanceByes();
        session()->flash('message', 'Bracket berhasil digenerate!');
    }

    /**
     * Auto-advance bye matches di Round 1 (jumlah tim ganjil).
     * Panggil setelah generateBracket().
     */
    public function advanceByes(): void
    {
        $round1Matches = GameMatch::where('tournament_id', $this->tournament->id)
            ->where('round', 1)
            ->where('status', 'pending')
            ->get();

        foreach ($round1Matches as $match) {
            $hasTeam1 = !is_null($match->team1_id);
            $hasTeam2 = !is_null($match->team2_id);

            // Normal match — skip
            if ($hasTeam1 && $hasTeam2) continue;

            // Both kosong — void slot, tidak ada yang di-advance
            if (!$hasTeam1 && !$hasTeam2) {
                $match->update(['status' => 'completed', 'finished_at' => now()]);
                continue;
            }

            // Bye: tepat satu tim — auto-advance ke ronde berikutnya
            $winnerId = $match->team1_id ?? $match->team2_id;
            $match->update([
                'status' => 'completed',
                'winner_team_id' => $winnerId,
                'score1' => $match->team1_id ? 1 : 0,
                'score2' => $match->team2_id ? 1 : 0,
                'games_detail' => json_encode([['t1' => $match->team1_id ? 1 : 0, 't2' => $match->team2_id ? 1 : 0]]),
                'finished_at' => now(),
            ]);

            // Tim bye maju ke slot ronde berikutnya
            if ($match->next_match_id) {
                $nextMatch = $match->nextMatch;
                if ($nextMatch) {
                    $column = 'team' . $match->next_slot . '_id';
                    $nextMatch->$column = $winnerId;
                    $nextMatch->save();
                }
            }
        }
    }

    // ========== SCORES ==========

    public function setGamesFormat(int $gamesToWin): void
    {
        if (! $this->ensureStatus('Format hanya bisa diubah saat status draft.', Tournament::STATUS_DRAFT)) {
            return;
        }
        $this->tournament->update(['games_to_win' => $gamesToWin]);
        $this->tournament->refresh();
        session()->flash('message', 'Format pertandingan diubah ke ' . ($gamesToWin === 1 ? '1 game (langsung selesai)' : 'Best of 3'));
    }

    public function startMatch($matchId)
    {
        if (! $this->ensureStatus('Mulai turnamen terlebih dahulu.', Tournament::STATUS_ONGOING)) {
            return;
        }

        $match = $this->tournament->gameMatches()->findOrFail($matchId);

        if (!$match->team1_id || !$match->team2_id) {
            session()->flash('error', 'Tidak bisa memulai pertandingan — tim tidak lengkap.');
            return;
        }

        $match->update(['status' => 'ongoing', 'started_at' => now()]);
        $match->initGames();
        $match->save();
        $this->tournament->load('gameMatches.team1', 'gameMatches.team2', 'gameMatches.winner');
    }

    public function editScore($matchId)
    {
        $match = $this->tournament->gameMatches()->findOrFail($matchId);
        $this->updatingMatchId = $matchId;
        $this->score1 = $match->score1;
        $this->score2 = $match->score2;
    }

    public function cancelEdit()
    {
        $this->reset(['updatingMatchId', 'score1', 'score2']);
    }

    public function saveScore()
    {
        if (! $this->ensureStatus('Skor hanya bisa diubah saat turnamen berjalan.', Tournament::STATUS_ONGOING)) {
            return;
        }

        $this->validate([
            'score1' => 'required|integer|min:0',
            'score2' => 'required|integer|min:0',
        ]);

        $match = $this->tournament->gameMatches()->findOrFail($this->updatingMatchId);

        if ($this->score1 === $this->score2) {
            session()->flash('error', 'Skor tidak boleh seri.');
            return;
        }

        $winnerId = $this->score1 > $this->score2 ? $match->team1_id : $match->team2_id;

        $match->update([
            'score1' => $this->score1,
            'score2' => $this->score2,
            'winner_team_id' => $winnerId,
            'status' => 'completed',
            'finished_at' => now(),
        ]);

        // Advance winner to next match
        if ($match->next_match_id) {
            $nextMatch = GameMatch::find($match->next_match_id);
            $column = 'team' . $match->next_slot . '_id';
            $nextMatch->$column = $winnerId;
            $nextMatch->save();
        } else {
            // Final match selesai, turnamen selesai
            $this->tournament->update(['status' => 'completed']);
        }

        $this->cancelEdit();
        $this->tournament->load('gameMatches.team1', 'gameMatches.team2', 'gameMatches.winner');
        session()->flash('message', 'Skor berhasil disimpan!');
    }

    // ========== TOURNAMENT ACTIONS ==========

    public function startTournament()
    {
        if (! $this->ensureStatus('Turnamen sudah dimulai.', Tournament::STATUS_DRAFT)) {
            return;
        }

        if ($this->tournament->teams()->count() < 2) {
            session()->flash('error', 'Buat tim dulu — minimal 2 tim sebelum turnamen dimulai.');
            return;
        }

        $hasReadyMatch = $this->tournament->gameMatches()
            ->where('round', 1)
            ->whereNotNull('team1_id')
            ->whereNotNull('team2_id')
            ->where('status', 'pending')
            ->exists();

        if (! $hasReadyMatch) {
            session()->flash('error', 'Generate bracket dulu — belum ada pertandingan yang siap dimainkan.');
            return;
        }

        $this->tournament->update(['status' => 'ongoing']);

        // Start first match that has both teams
        $firstMatch = $this->tournament->gameMatches()
            ->where('round', 1)
            ->whereNotNull('team1_id')
            ->whereNotNull('team2_id')
            ->where('status', 'pending')
            ->first();

        if ($firstMatch) {
            $firstMatch->update(['status' => 'ongoing', 'started_at' => now()]);
        }

        $this->tournament->load('gameMatches');
        session()->flash('message', 'Turnamen dimulai!');
    }

    public function resetTournament()
    {
        if (! $this->ensureStatus('Turnamen yang diarsipkan tidak bisa direset.', Tournament::STATUS_DRAFT, Tournament::STATUS_ONGOING, Tournament::STATUS_COMPLETED)) {
            return;
        }

        $this->tournament->gameMatches()->delete();
        $this->tournament->teams()->delete();
        $this->tournament->update(['status' => 'draft', 'original_status' => null]);
        $this->tournament->load(['teams', 'gameMatches']);
        session()->flash('message', 'Turnamen direset.');
    }

    public function archiveTournament()
    {
        if ($this->tournament->status === 'archived') return;

        $this->tournament->update([
            'original_status' => $this->tournament->status,
            'status' => 'archived',
        ]);

        $this->redirect(route('tournaments.index'));
    }

    public function unarchiveTournament()
    {
        if ($this->tournament->status !== 'archived') return;

        $this->tournament->update([
            'status' => $this->tournament->original_status ?? 'draft',
            'original_status' => null,
        ]);

        $this->redirect(route('tournaments.index'));
    }

    // ========== STATE GUARD ==========

    /**
     * Estimasi total waktu pertandingan (single elimination).
     * - Tim = peserta ÷ 2 (2 peserta = 1 tim)
     * - Pertandingan = tim - 1 (bye auto-advance, tidak makan waktu)
     * - Durasi per match: 1 game ±17 mnt, best of 3 ±35 mnt (level komunitas)
     * - Plus ±5 mnt jeda antar pertandingan, dibagi jumlah lapangan
     */
    public function estimateSummary(): array
    {
        $teamsCount = $this->tournament->teams()->count();
        if ($teamsCount < 2) {
            $teamsCount = (int) ceil($this->tournament->participants()->count() / 2);
        }

        $matches = max($teamsCount - 1, 0);
        $perMatch = $this->tournament->games_to_win === 1 ? 17 : 35;
        $break = 5;
        $courts = max($this->estimateCourts, 1);
        $totalMinutes = (int) round($matches * ($perMatch + $break) / $courts);

        return [
            'teams' => $teamsCount,
            'matches' => $matches,
            'perMatch' => $perMatch,
            'break' => $break,
            'courts' => $courts,
            'totalMinutes' => $totalMinutes,
            'totalLabel' => $totalMinutes >= 60
                ? '±' . intdiv($totalMinutes, 60) . ' jam ' . ($totalMinutes % 60) . ' menit'
                : '±' . $totalMinutes . ' menit',
            'formatLabel' => $this->tournament->games_to_win === 1 ? '1 Game' : 'Best of 3',
        ];
    }

    /**
     * Pastikan turnamen berada pada salah satu status yang diizinkan.
     * Kalau tidak: flash error dan return false (aksi dibatalkan).
     */
    private function ensureStatus(string $message, string ...$allowed): bool
    {
        if (in_array($this->tournament->status, $allowed, true)) {
            return true;
        }

        session()->flash('error', $message);
        return false;
    }

    // ========== RENDER ==========

    public function render()
    {
        $bracketRounds = collect();
        $champion = null;

        // Hanya load data yang diperlukan untuk tab aktif
        match ($this->tab) {
            'participants' => $this->tournament->load('participants'),
            'teams' => $this->tournament->load('teams.members'),
            'bracket' => null, // loaded below
            default => $this->tournament->load('participants'),
        };

        if ($this->tab === 'bracket') {
            $this->tournament->load([
                'gameMatches' => fn ($q) => $q->orderBy('round')->orderBy('match_number'),
                'gameMatches.team1.members',
                'gameMatches.team2.members',
                'gameMatches.winner',
            ]);

            $bracketRounds = $this->tournament->gameMatches
                ->groupBy('round')
                ->sortKeys();

            if ($this->tournament->status === 'completed') {
                $finalMatch = $this->tournament->gameMatches()
                    ->whereNull('next_match_id')
                    ->where('status', 'completed')
                    ->first();
                $champion = $finalMatch?->winner;
            }
        }

        return view('livewire.tournament-show', [
            'bracketRounds' => $bracketRounds,
            'bracketLayout' => $this->tab === 'bracket' ? $this->bracketLayout($this->tournament->gameMatches, cardH: 160) : [],
            'champion' => $champion,
            'estimate' => $this->estimateSummary(),
        ]);
    }
}
