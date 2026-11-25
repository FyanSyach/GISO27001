<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** Data uji Certificate; tidak memberikan reward bisnis secara otomatis.
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    /** Menghasilkan atribut valid beserta induk relasi yang diperlukan.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'certificate_number' => 'GISO-'.fake()->unique()->uuid(), 'file_path' => 'certificates/sample.pdf', 'issued_at' => now()];
    }
}
