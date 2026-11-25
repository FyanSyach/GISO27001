<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji QuizAttempt; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'quiz_id' => Quiz::factory(), 'selected_answer' => 'A', 'is_correct' => true, 'attempt_number' => 1, 'points_earned' => 0];
    }
}
