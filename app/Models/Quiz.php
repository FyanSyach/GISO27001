<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Entitas Quiz yang memetakan tabel quizzes. */
class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quizzes';

    protected $fillable = [
        0 => 'module_id',
        1 => 'question',
        2 => 'options',
        3 => 'correct_answer',
        4 => 'explanation',
        5 => 'points_reward',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    /** Relasi module untuk entitas Quiz. */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** Relasi attempts untuk entitas Quiz. */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
