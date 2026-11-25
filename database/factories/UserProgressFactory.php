<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji UserProgress; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<UserProgress>
 */
class UserProgressFactory extends Factory
{
    protected $model = UserProgress::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'module_id' => Module::factory(), 'is_completed' => false, 'points_awarded' => false];
    }
}
