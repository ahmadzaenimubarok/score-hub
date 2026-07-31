<?php

namespace App\Livewire;

use App\Models\PublicMatch;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.standalone')]
class PublicScoreboardCreate extends Component
{
    public string $nameA = '';
    public string $nameB = '';
    public int $gamesToWin = 2;

    public function create()
    {
        $this->validate([
            'nameA' => 'nullable|string|max:60',
            'nameB' => 'nullable|string|max:60',
            'gamesToWin' => 'required|in:1,2',
        ]);

        $match = PublicMatch::create([
            'name_a' => trim($this->nameA) ?: 'Pemain 1',
            'name_b' => trim($this->nameB) ?: 'Pemain 2',
            'games_to_win' => $this->gamesToWin,
        ]);
        $match->initGames();

        $this->redirect('/s/' . $match->code);
    }

    public function render()
    {
        return view('livewire.public-scoreboard-create');
    }
}
