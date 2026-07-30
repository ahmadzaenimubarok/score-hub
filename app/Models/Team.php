<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['tournament_id', 'name'];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'team_members')
            ->withTimestamps();
    }

    public function membersList(): string
    {
        return $this->members->pluck('name')->join(' & ');
    }
}
