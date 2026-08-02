<?php

namespace Tests\Feature;

use App\Livewire\TournamentShow;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_teams_avoids_same_group_pairing_when_groups_active(): void
    {
        $t = Tournament::create([
            'name' => 'Grup Test',
            'use_groups' => true,
            'group_count' => 3,
            'group_names' => ['Putra', 'Putri', 'Campuran'],
        ]);

        // 4 Putra, 2 Putri, 2 Campuran = 8 peserta -> 4 tim
        $rows = [
            ['A1', 'Putra'], ['A2', 'Putra'], ['A3', 'Putra'], ['A4', 'Putra'],
            ['B1', 'Putri'], ['B2', 'Putri'],
            ['C1', 'Campuran'], ['C2', 'Campuran'],
        ];
        foreach ($rows as [$name, $group]) {
            $t->participants()->create(['name' => $name, 'group_name' => $group]);
        }

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(4, $t->teams()->count(), 'Harusnya ada 4 tim untuk 8 peserta');

        foreach ($t->teams as $team) {
            $members = $team->members->pluck('group_name');
            $this->assertNotEquals(
                $members[0] ?? null,
                $members[1] ?? null,
                "Tim {$team->name} berisi dua peserta sekelompok: {$members->join(' & ')}"
            );
        }
    }

    public function test_generate_teams_still_pairs_within_group_when_inevitable(): void
    {
        $t = Tournament::create([
            'name' => 'Satu Kelompok Besar',
            'use_groups' => true,
            'group_count' => 2,
            'group_names' => ['Putra', 'Putri'],
        ]);

        // 5 Putra, 1 Putri = 6 peserta -> 3 tim. Putri hanya bisa berpasangan dengan 1 Putra,
        // sisanya 4 Putra -> 2 tim Putra-Putra (tak terhindarkan).
        foreach (['A1', 'A2', 'A3', 'A4', 'A5'] as $n) {
            $t->participants()->create(['name' => $n, 'group_name' => 'Putra']);
        }
        $t->participants()->create(['name' => 'B1', 'group_name' => 'Putri']);

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(3, $t->teams()->count(), 'Harusnya ada 3 tim untuk 6 peserta');
        $this->assertEquals(6, $t->teams->sum(fn ($team) => $team->members->count()), 'Semua peserta harus masuk tim');
    }

    public function test_generate_teams_without_groups_matches_old_behavior(): void
    {
        $t = Tournament::create(['name' => 'Tanpa Kelompok', 'use_groups' => false]);

        foreach (['A', 'B', 'C', 'D', 'E'] as $n) {
            $t->participants()->create(['name' => $n, 'group_name' => null]);
        }

        Livewire::test(TournamentShow::class, ['tournament' => $t])
            ->call('generateTeams')
            ->assertHasNoErrors();

        $t->load('teams.members');
        $this->assertEquals(3, $t->teams()->count(), '5 peserta -> 2 tim berpasangan + 1 tim solo');
        $this->assertEquals(5, $t->teams->sum(fn ($team) => $team->members->count()));
    }
}
