<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas UserBadge yang memetakan tabel user_badges. */
class UserBadge extends Model
{
    use HasFactory;

    protected $table = 'user_badges';

    protected $fillable = [
        0 => 'user_id',
        1 => 'badge_id',
        2 => 'earned_at',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
        ];
    }

    /** Relasi user untuk entitas UserBadge. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi badge untuk entitas UserBadge. */
    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}
