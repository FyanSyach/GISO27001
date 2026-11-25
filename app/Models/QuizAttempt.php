<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Entitas QuizAttempt yang memetakan tabel quiz_attempts. */
class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';

    protected $fillable = [
        0 => 'user_id',
        1 => 'quiz_id',
        2 => 'selected_answer',
        3 => 'is_correct',
        4 => 'attempt_number',
        5 => 'points_earned',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    /** Relasi user untuk entitas QuizAttempt. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi quiz untuk entitas QuizAttempt. */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
