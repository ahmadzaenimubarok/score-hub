<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    // Status machine — sumber kebenaran tunggal untuk nilai status turnamen.
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_ONGOING,
        self::STATUS_COMPLETED,
        self::STATUS_ARCHIVED,
    ];

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

    /** Label bahasa Indonesia untuk badge status di UI. */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ONGOING => 'Berjalan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_ARCHIVED => 'Diarsipkan',
            default => ucfirst((string) $this->status),
        };
    }
}
