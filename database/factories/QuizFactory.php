<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji Quiz; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['module_id' => Module::factory(), 'question' => 'Bagaimana merespons email mencurigakan?', 'options' => ['A' => 'Laporkan ke tim keamanan', 'B' => 'Klik tautan', 'C' => 'Bagikan password'], 'correct_answer' => 'A', 'explanation' => 'Laporkan melalui kanal resmi.', 'points_reward' => 10];
    }
}
