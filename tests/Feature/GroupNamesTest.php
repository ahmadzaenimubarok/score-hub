<?php

namespace Tests\Feature;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GroupNamesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_tournament_with_custom_group_names(): void
    {
        Livewire::test(\App\Livewire\TournamentIndex::class)
            ->set('newName', 'Liga Malam Minggu')
            ->set('newUseGroups', true)
            ->set('newGroupNames', ['Grup A', 'Grup B', 'Grup C'])
            ->call('create');

        $t = Tournament::where('name', 'Liga Malam Minggu')->firstOrFail();
        $this->assertTrue($t->use_groups);
        $this->assertEquals(3, $t->group_count);
        $this->assertEquals(['Grup A', 'Grup B', 'Grup C'], $t->group_names);
        $this->assertEquals([
            1 => 'Grup A',
            2 => 'Grup B',
            3 => 'Grup C',
        ], $t->groupOptions());
    }

    public function test_checking_groups_adds_first_field_and_add_button_appends(): void
    {
        $c = Livewire::test(\App\Livewire\TournamentIndex::class);

        // Centang → 1 field muncul
        $c->set('newUseGroups', true)
            ->assertSet('newGroupNames', ['']);

        // Klik add → field baru di bawah
        $c->call('addGroupName')
            ->assertSet('newGroupNames', ['', '']);

        // Isi dua field, tambah lagi
        $c->set('newGroupNames', ['Putra', 'Putri'])
            ->call('addGroupName')
            ->assertSet('newGroupNames', ['Putra', 'Putri', '']);

        // Hapus field tengah → reindex
        $c->call('removeGroupName', 1)
            ->assertSet('newGroupNames', ['Putra', '']);
    }

    public function test_legacy_tournament_without_group_names_falls_back(): void
    {
        // Data lama (sebelum fitur nama custom): group_names null → fallback "Kelompok N"
        $t = Tournament::create([
            'name' => 'Turnamen Lama',
            'use_groups' => true,
            'group_count' => 2,
        ]);

        $this->assertEquals([
            1 => 'Kelompok 1',
            2 => 'Kelompok 2',
        ], $t->groupOptions());
    }

    public function test_create_tournament_rejects_blank_or_duplicate_group_names(): void
    {
        // Blank
        Livewire::test(\App\Livewire\TournamentIndex::class)
            ->set('newName', 'Kosong')
            ->set('newUseGroups', true)
            ->set('newGroupNames', ['Grup A', ''])
            ->call('create')
            ->assertHasErrors(['newGroupNames']);

        // Duplicate
        Livewire::test(\App\Livewire\TournamentIndex::class)
            ->set('newName', 'Duplikat')
            ->set('newUseGroups', true)
            ->set('newGroupNames', ['Grup A', 'Grup A'])
            ->call('create')
            ->assertHasErrors(['newGroupNames']);

        $this->assertNull(Tournament::where('name', 'Kosong')->first());
        $this->assertNull(Tournament::where('name', 'Duplikat')->first());
    }

    public function test_registration_page_shows_custom_group_names(): void
    {
        $t = Tournament::create([
            'name' => 'Dengan Custom',
            'use_groups' => true,
            'group_count' => 2,
            'group_names' => ['Putra', 'Putri'],
        ]);

        Livewire::test(\App\Livewire\RegistrationPage::class, ['code' => $t->code])
            ->assertSee('Putra')
            ->assertSee('Putri');
    }
}
