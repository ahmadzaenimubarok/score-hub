<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    protected $fillable = ['name', 'code', 'status', 'original_status', 'games_to_win'];

    protected $attributes = [
        'status' => 'draft',
        'games_to_win' => 2,
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'games_to_win' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tournament $tournament) {
            if (empty($tournament->code)) {
                $tournament->code = strtoupper(Str::random(6));
            }
        });
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function gameMatches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }
}
