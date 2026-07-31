<?php

namespace App\Livewire;

use App\Models\Tournament;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.register')]
class RegistrationPage extends Component
{
    public Tournament $tournament;
    public string $code;

    public string $name = '';

    public function mount(string $code)
    {
        $this->code = $code;
        $this->tournament = Tournament::where('code', $code)
            ->with('participants')
            ->firstOrFail();
    }

    public function register()
    {
        if ($this->tournament->status !== 'draft') {
            session()->flash('error', 'Pendaftaran sudah ditutup.');
            return;
        }

        $this->name = trim($this->name);
        $this->validate(['name' => 'required|string|max:255']);

        $exists = $this->tournament->participants()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($this->name)])
            ->exists();

        if ($exists) {
            session()->flash('error', 'Nama "' . $this->name . '" sudah terdaftar.');
            return;
        }

        $this->tournament->participants()->create([
            'name' => $this->name,
        ]);

        $this->name = '';
        $this->tournament->load('participants');
        session()->flash('message', 'Pendaftaran berhasil! Nama kamu sudah masuk daftar.');
    }

    public function render()
    {
        return view('livewire.registration-page');
    }
}
