<?php

use App\Livewire\PublicBracket;
use App\Livewire\RegistrationPage;
use App\Livewire\Scoreboard;
use App\Livewire\TournamentIndex;
use App\Livewire\TournamentShow;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::prefix('admin')->group(function () {
    Route::get('/', TournamentIndex::class)->name('tournaments.index');
    Route::get('/tournaments/{tournament}', TournamentShow::class)->name('tournaments.show');
});

Route::get('/scoreboard/{gameMatch}', Scoreboard::class)->name('scoreboard.show');
Route::get('/t/{code}/scoreboard/{gameMatch}', Scoreboard::class)->name('public.scoreboard');
Route::get('/t/{code}', PublicBracket::class)->name('public.bracket');
Route::get('/r/{code}', RegistrationPage::class)->name('registration.show');
