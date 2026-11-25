<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas UserProgress yang memetakan tabel user_progress. */
class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        0 => 'user_id',
        1 => 'module_id',
        2 => 'is_completed',
        3 => 'points_awarded',
        4 => 'completed_at',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'points_awarded' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /** Relasi user untuk entitas UserProgress. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi module untuk entitas UserProgress. */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
