<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Entitas ForumThread yang memetakan tabel forum_threads. */
class ForumThread extends Model
{
    use HasFactory;

    protected $table = 'forum_threads';

    protected $fillable = [
        0 => 'user_id',
        1 => 'category',
        2 => 'title',
        3 => 'content',
        4 => 'best_reply_id',
        5 => 'is_locked',
    ];

    /** Mengonversi atribut database ke tipe domain. */
    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
        ];
    }

    /** Relasi user untuk entitas ForumThread. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Relasi replies untuk entitas ForumThread. */
    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    /** Relasi bestReply untuk entitas ForumThread. */
    public function bestReply(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'best_reply_id');
    }
}
