<?php

namespace App\Policies;

use App\Models\ForumThread;
use App\Models\User;

/** Membatasi perubahan diskusi pada pemilik; admin dapat memoderasi penghapusan. */
class ForumThreadPolicy
{
    /** Pemilik boleh menyunting thread yang tidak dikunci. */
    public function update(User $user, ForumThread $thread): bool
    {
        return $user->id === $thread->user_id && ! $thread->is_locked;
    }

    /** Pemilik atau administrator boleh menghapus diskusi. */
    public function delete(User $user, ForumThread $thread): bool
    {
        return $user->id === $thread->user_id || $user->isAdmin();
    }
}
