<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Menghitung streak berdasarkan hari kalender timezone aplikasi, bukan sesi login. */
class StreakTrackerService
{
    /** Mencatat aktivitas belajar sah satu kali per hari dan memberikan bonus harian 10 poin. */
    public function track(User $user): User
    {
        return DB::transaction(function () use ($user): User {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $today = today();
            if ($locked->last_active_date && $locked->last_active_date->gte($today)) {
                return $locked;
            }
            $locked->current_streak = $locked->last_active_date?->isSameDay($today->copy()->subDay()) ? $locked->current_streak + 1 : 1;
            $locked->last_active_date = $today;
            $locked->save();

            return app(GamificationService::class)->grantPoints($locked, 10);
        }, 3);
    }
}
