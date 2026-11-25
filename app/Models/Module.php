<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Entitas Module yang memetakan tabel modules. */
class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';

    protected $fillable = [
        0 => 'title',
        1 => 'slug',
        2 => 'description',
        3 => 'iso_control_ref',
        4 => 'order_level',
        5 => 'passing_score',
        6 => 'points_reward',
        7 => 'is_published',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'order_level' => 'integer',
            'passing_score' => 'integer',
            'points_reward' => 'integer',
        ];
    }

    /** Relasi contents untuk entitas Module. */
    public function contents(): HasMany
    {
        return $this->hasMany(ModuleContent::class);
    }

    /** Relasi quizzes untuk entitas Module. */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /** Relasi progress untuk entitas Module. */
    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }
}
