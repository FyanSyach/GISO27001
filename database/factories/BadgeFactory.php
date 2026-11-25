<?php

namespace Database\Factories;

use App\Enums\BadgeCategory;
use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji Badge; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['code' => fake()->unique()->slug(3), 'name' => fake()->words(3, true), 'description' => fake()->sentence(), 'icon_path' => 'images/badges/guardian.svg', 'category' => BadgeCategory::POINTS, 'min_points' => 100];
    }
}
