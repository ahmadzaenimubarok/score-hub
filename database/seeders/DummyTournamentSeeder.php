<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\Participant;
use App\Models\Team;
use App\Models\GameMatch;
use Illuminate\Database\Seeder;

class DummyTournamentSeeder extends Seeder
{
    public function run(): void
    {
        $tournament = Tournament::create([
            'name' => 'Kejuaraan Bulu Tangkis Terbuka 2025',
            'code' => 'BATAN25',
            'status' => 'draft',
            'games_to_win' => 2,
        ]);

        $playerNames = [
            'Agus Pratama', 'Budi Santoso', 'Citra Dewi', 'Dian Permata',
            'Eko Saputra', 'Fitri Handayani', 'Gilang Ramadhan', 'Hesti Utami',
            'Irfan Maulana', 'Joko Susilo', 'Kartika Sari', 'Lukman Hakim',
            'Maya Anggraini', 'Nugroho Wicaksono', 'Putri Ayuningtyas',
        ];

        $participants = collect();
        foreach ($playerNames as $name) {
            $participants->push(Participant::create([
                'tournament_id' => $tournament->id,
                'name' => $name,
            ]));
        }

        $teams = collect();
        foreach ($participants as $participant) {
            $team = Team::create([
                'tournament_id' => $tournament->id,
                'name' => $participant->name,
            ]);
            $team->members()->attach($participant);
            $teams->push($team);
        }

        $this->createBracket($tournament, $teams);
    }

    private function createBracket(Tournament $tournament, $teams): void
    {
        $teamIds = $teams->pluck('id')->values();
        $teamCount = $teamIds->count();

        $round = 1;
        $matches = [];

        $powerOfTwo = 1;
        while ($powerOfTwo < $teamCount) $powerOfTwo *= 2;

        $firstRoundByes = $powerOfTwo - $teamCount;
        $firstRoundMatches = ($teamCount - $firstRoundByes) / 2;

        for ($i = 0; $i < $firstRoundMatches; $i++) {
            $t1 = $teamIds[$i * 2];
            $t2 = $teamIds[$i * 2 + 1];
            $matches[] = GameMatch::create([
                'tournament_id' => $tournament->id,
                'round' => 1,
                'match_number' => $i + 1,
                'team1_id' => $t1,
                'team2_id' => $t2,
                'status' => 'pending',
            ]);
        }

        $lastMatchNum = $firstRoundMatches;

        if ($firstRoundByes > 0) {
            $byeTeamStartIndex = $teamCount - $firstRoundByes;
            for ($i = 0; $i < $firstRoundByes; $i++) {
                $team = $teams[$byeTeamStartIndex + $i];
                $matches[] = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => 1,
                    'match_number' => $lastMatchNum + $i + 1,
                    'team1_id' => $team->id,
                    'status' => 'completed',
                    'winner_team_id' => $team->id,
                    'started_at' => now(),
                    'finished_at' => now(),
                ]);
            }
            $lastMatchNum += $firstRoundByes;
        }

        $teamsInRound = $firstRoundMatches + $firstRoundByes;

        $prevRoundStart = 0;
        $prevRoundCount = $teamsInRound;

        while ($teamsInRound > 1) {
            $round++;
            $matchesInRound = intdiv($teamsInRound, 2);

            for ($i = 0; $i < $matchesInRound; $i++) {
                $prevIdx = $prevRoundStart + $i * 2;

                $match = GameMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'match_number' => $i + 1,
                    'status' => 'pending',
                ]);

                if (isset($matches[$prevIdx])) {
                    $matches[$prevIdx]->update([
                        'next_match_id' => $match->id,
                        'next_slot' => 1,
                    ]);
                }
                if (isset($matches[$prevIdx + 1])) {
                    $matches[$prevIdx + 1]->update([
                        'next_match_id' => $match->id,
                        'next_slot' => 2,
                    ]);
                }

                $matches[] = $match;
            }

            $prevRoundStart = count($matches) - $matchesInRound;
            $teamsInRound = $matchesInRound;
        }
    }
}
