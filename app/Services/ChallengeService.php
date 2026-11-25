<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Support\Facades\DB;

/** Memproses progres challenge dari event server, bukan klaim jumlah dari browser. */
class ChallengeService
{
    /** Menambahkan satu penyelesaian modul pertama ke challenge aktif dan mengklaim reward sekali. */
    public function moduleCompleted(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $challenges = Challenge::where('target_type', 'module_completion')->where('starts_at', '<=', now())->where('ends_at', '>', now())->get();
            foreach ($challenges as $challenge) {
                $progress = UserChallenge::firstOrCreate(['user_id' => $locked->id, 'challenge_id' => $challenge->id]);
                if ($progress->is_completed) {
                    continue;
                }
                $progress->progress_count = min($challenge->target_count, $progress->progress_count + 1);
                if ($progress->progress_count >= $challenge->target_count) {
                    $progress->is_completed = true;
                    $progress->completed_at = now();
                    app(GamificationService::class)->grantPoints($locked, $challenge->points_reward);
                }
                $progress->save();
            }
        }, 3);
    }
}
