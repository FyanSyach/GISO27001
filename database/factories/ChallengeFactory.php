<?php

namespace Database\Factories;

use App\Models\Challenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji Challenge; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<Challenge>
 */
class ChallengeFactory extends Factory
{
    protected $model = Challenge::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['title' => 'Belajar hari ini', 'description' => 'Selesaikan satu modul.', 'frequency' => 'daily', 'target_type' => 'module_completion', 'target_count' => 1, 'points_reward' => 10, 'starts_at' => now()->startOfDay(), 'ends_at' => now()->endOfDay()];
    }
}
