<?php

namespace Database\Factories;

use App\Enums\ContentType;
use App\Models\Module;
use App\Models\ModuleContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji ModuleContent; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<ModuleContent>
 */
class ModuleContentFactory extends Factory
{
    protected $model = ModuleContent::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['module_id' => Module::factory(), 'title' => fake()->sentence(3), 'content_type' => ContentType::TEXT, 'body' => fake()->paragraph(), 'duration_minutes' => 5, 'sort_order' => 1];
    }
}
