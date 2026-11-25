<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Facades\DB;

/** Pusat mutasi poin dan atribusi badge; pemanggil wajib menjaga idempotensi event bisnis. */
class GamificationService
{
    /** Menambahkan poin nonnegatif secara terkunci, memperbarui level dan badge; mengembalikan pengguna terbaru. */
    public function grantPoints(User $user, int $points): User
    {
        return DB::transaction(function () use ($user, $points): User {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $locked->points += max(0, $points);
            $locked->level = min(10, intdiv($locked->points, 100) + 1);
            $locked->save();
            $badges = Badge::where(function ($query) use ($locked): void {
                $query->where(function ($query) use ($locked): void {
                    $query->where('category', 'points')->whereNotNull('min_points')->where('min_points', '<=', $locked->points);
                })->orWhere(function ($query) use ($locked): void {
                    $query->where('category', 'module')->whereIn('required_module_id', $locked->progress()->where('is_completed', true)->select('module_id'));
                });
            })->get();
            foreach ($badges as $badge) {
                // firstOrCreate mempertahankan waktu perolehan dan constraint unik mencegah duplikasi.
                UserBadge::firstOrCreate(['user_id' => $locked->id, 'badge_id' => $badge->id], ['earned_at' => now()]);
            }

            return $locked;
        }, 3);
    }
}
