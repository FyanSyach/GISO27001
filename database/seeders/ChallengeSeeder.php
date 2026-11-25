<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;

/** Menyediakan tantangan periode kalender tanpa menggandakan periode yang sama. */
class ChallengeSeeder extends Seeder
{
    /** Membuat challenge harian dan mingguan; periode berakhir secara eksklusif. */
    public function run(): void
    {
        foreach (['daily', 'weekly'] as $frequency) {
            $start = $frequency === 'daily' ? today() : today()->startOfWeek();
            $end = $frequency === 'daily' ? $start->copy()->addDay() : $start->copy()->addWeek();
            Challenge::firstOrCreate(['frequency' => $frequency, 'target_type' => 'module_completion', 'starts_at' => $start], [
                'title' => $frequency === 'daily' ? 'Langkah Aman Hari Ini' : 'Pejuang Keamanan Mingguan',
                'description' => 'Selesaikan modul baru selama periode tantangan.', 'target_count' => $frequency === 'daily' ? 1 : 3,
                'points_reward' => $frequency === 'daily' ? 10 : 30, 'ends_at' => $end,
            ]);
        }
    }
}
