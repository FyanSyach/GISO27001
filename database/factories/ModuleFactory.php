<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji Module; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    protected $model = Module::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['title' => fake()->sentence(4), 'slug' => fake()->unique()->slug(), 'description' => fake()->paragraph(), 'iso_control_ref' => 'A.6.3', 'order_level' => fake()->unique()->numberBetween(100, 100000), 'passing_score' => 70, 'points_reward' => 50, 'is_published' => false];
    }
}
