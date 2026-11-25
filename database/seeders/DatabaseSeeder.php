<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Menjalankan seed Fase 2 secara atomik dan sesuai urutan dependensi. */
class DatabaseSeeder extends Seeder
{
    /** Mengisi akun awal, modul, dan badge; kegagalan membatalkan seluruh perubahan seed. */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call([UserSeeder::class, ModuleSeeder::class, BadgeSeeder::class, LearningContentSeeder::class, ChallengeSeeder::class]);
        });
    }
}
