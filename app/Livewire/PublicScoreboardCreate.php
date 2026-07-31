<?php

namespace App\Livewire;

use App\Models\PublicMatch;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.standalone')]
class PublicScoreboardCreate extends Component
{
    public function create()
    {
        $match = PublicMatch::create([
            'name_a' => 'Tim 1',
            'name_b' => 'Tim 2',
            'games_to_win' => 2,
        ]);
        $match->initGames();

        $this->redirect('/s/' . $match->code);
    }

    public function render()
    {
        return view('livewire.public-scoreboard-create');
    }
}
