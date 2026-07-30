<?php

namespace App\Livewire;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TournamentIndex extends Component
{
    use WithPagination;

    public string $newName = '';

    public function create()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
        ]);

        Tournament::create(['name' => $this->newName]);

        $this->newName = '';
        session()->flash('message', 'Turnamen berhasil dibuat.');
    }

    public function render()
    {
        return view('livewire.tournament-index', [
            'tournaments' => Tournament::withCount(['participants', 'teams'])->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
