<?php

namespace Database\Seeders;

use App\Enums\BadgeCategory;
use App\Models\Badge;
use App\Models\Module;
use Illuminate\Database\Seeder;

/** Master badge awal tanpa mengatribusikan badge kepada pengguna. */
class BadgeSeeder extends Seeder
{
    /** Mengisi kriteria badge secara idempoten berdasarkan kode. */
    public function run(): void
    {
        $badges = [
            ['guardian-of-data', 'Guardian of Data', 'Mengumpulkan 100 poin.', BadgeCategory::POINTS, 100, null],
            ['security-champion', 'Security Champion', 'Mengumpulkan 500 poin.', BadgeCategory::POINTS, 500, null],
            ['fast-learner', 'Fast Learner', 'Menuntaskan modul dasar keamanan informasi.', BadgeCategory::MODULE, null, Module::where('slug', 'dasar-keamanan-informasi')->sole()->id],
        ];

        foreach ($badges as [$code, $name, $description, $category, $points, $moduleId]) {
            Badge::firstOrCreate(['code' => $code], [
                'name' => $name, 'description' => $description,
                'icon_path' => 'images/badges/guardian.svg', 'category' => $category,
                'min_points' => $points, 'required_module_id' => $moduleId,
            ]);
        }
    }
}
