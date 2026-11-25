<?php

namespace Database\Factories;

use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji ForumThread; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<ForumThread>
 */
class ForumThreadFactory extends Factory
{
    protected $model = ForumThread::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'category' => 'Keamanan informasi', 'title' => fake()->sentence(), 'content' => fake()->paragraph(), 'is_locked' => false];
    }
}
