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
    public bool $showArchived = false;

    public function create()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
        ]);

        Tournament::create(['name' => $this->newName]);

        $this->newName = '';
        $this->resetPage();
        session()->flash('message', 'Turnamen berhasil dibuat.');
    }

    public function showActive()
    {
        $this->showArchived = false;
        $this->resetPage();
    }

    public function showArchive()
    {
        $this->showArchived = true;
        $this->resetPage();
    }

    public function render()
    {
        $query = Tournament::withCount(['participants', 'teams'])->orderBy('created_at', 'desc');

        if ($this->showArchived) {
            $query->where('status', 'archived');
        } else {
            $query->where('status', '!=', 'archived');
        }

        return view('livewire.tournament-index', [
            'tournaments' => $query->paginate(10),
        ]);
    }
}
