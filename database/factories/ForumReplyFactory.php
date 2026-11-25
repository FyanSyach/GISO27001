<?php

namespace Database\Factories;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji ForumReply; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<ForumReply>
 */
class ForumReplyFactory extends Factory
{
    protected $model = ForumReply::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['thread_id' => ForumThread::factory(), 'user_id' => User::factory(), 'content' => fake()->paragraph()];
    }
}
