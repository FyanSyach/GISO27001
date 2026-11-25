<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Entitas Challenge yang memetakan tabel challenges. */
class Challenge extends Model
{
    use HasFactory;

    protected $table = 'challenges';

    protected $fillable = [
        0 => 'title',
        1 => 'description',
        2 => 'frequency',
        3 => 'target_type',
        4 => 'target_count',
        5 => 'points_reward',
        6 => 'starts_at',
        7 => 'ends_at',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /** Relasi userChallenges untuk entitas Challenge. */
    public function userChallenges(): HasMany
    {
        return $this->hasMany(UserChallenge::class);
    }
}
