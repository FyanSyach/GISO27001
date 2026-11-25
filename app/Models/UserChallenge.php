<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas UserChallenge yang memetakan tabel user_challenges. */
class UserChallenge extends Model
{
    use HasFactory;

    protected $table = 'user_challenges';

    protected $fillable = [
        0 => 'user_id',
        1 => 'challenge_id',
        2 => 'progress_count',
        3 => 'is_completed',
        4 => 'completed_at',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /** Relasi user untuk entitas UserChallenge. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi challenge untuk entitas UserChallenge. */
    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}
