<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji UserChallenge; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<UserChallenge>
 */
class UserChallengeFactory extends Factory
{
    protected $model = UserChallenge::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'challenge_id' => Challenge::factory(), 'progress_count' => 0, 'is_completed' => false];
    }
}
