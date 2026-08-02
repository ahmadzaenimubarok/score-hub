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
    public ?int $newMax = null;
    public bool $newUseGroups = false;
    public array $newGroupNames = [];

    public function updatedNewUseGroups(bool $value): void
    {
        if ($value && empty($this->newGroupNames)) {
            $this->newGroupNames = [''];
        }
        if (! $value) {
            $this->newGroupNames = [];
        }
    }

    public function addGroupName(): void
    {
        $this->newGroupNames[] = '';
    }

    public function removeGroupName(int $index): void
    {
        unset($this->newGroupNames[$index]);
        $this->newGroupNames = array_values($this->newGroupNames);
    }

    public function create()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newMax' => 'nullable|integer|in:4,8,16,32,64,128',
        ]);

        $groupNames = null;
        if ($this->newUseGroups) {
            if (empty($this->newGroupNames)) {
                $this->addError('newGroupNames', 'Tambahkan minimal 1 nama kelompok.');
                return;
            }

            $names = array_map(fn ($n) => trim((string) $n), $this->newGroupNames);
            if (in_array('', $names, true)) {
                $this->addError('newGroupNames', 'Nama kelompok tidak boleh kosong.');
                return;
            }
            if (count(array_unique($names)) !== count($names)) {
                $this->addError('newGroupNames', 'Nama kelompok tidak boleh ada yang sama.');
                return;
            }
            $groupNames = $names;
        }

        Tournament::create([
            'name' => $this->newName,
            'max_participants' => $this->newMax,
            'use_groups' => $this->newUseGroups,
            'group_count' => $this->newUseGroups ? count($groupNames) : null,
            'group_names' => $groupNames,
        ]);

        $this->newName = '';
        $this->newMax = null;
        $this->newUseGroups = false;
        $this->newGroupNames = [];
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
