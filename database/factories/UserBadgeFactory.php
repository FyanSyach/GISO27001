<?php

namespace Database\Factories;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji UserBadge; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<UserBadge>
 */
class UserBadgeFactory extends Factory
{
    protected $model = UserBadge::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'badge_id' => Badge::factory(), 'earned_at' => now()];
    }
}
