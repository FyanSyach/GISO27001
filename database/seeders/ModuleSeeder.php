<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

/** Menyediakan lima modul awareness dasar; tidak menimpa materi yang sudah diedit. */
class ModuleSeeder extends Seeder
{
    /** Mengisi master modul secara idempoten berdasarkan slug. */
    public function run(): void
    {
        $modules = [
            ['dasar-keamanan-informasi', 'Dasar Keamanan Informasi', 'A.6.3', 'Kenali kerahasiaan, integritas, dan ketersediaan informasi.'],
            ['waspada-phishing', 'Waspada Phishing dan Rekayasa Sosial', 'A.6.3', 'Kenali pesan mencurigakan dan laporkan melalui kanal resmi.'],
            ['password-dan-mfa', 'Password Kuat dan MFA', 'A.5.17', 'Gunakan password unik, pengelola password, dan autentikasi multifaktor.'],
            ['perlindungan-data', 'Perlindungan Data dan Privasi', 'A.5.34', 'Bagikan data hanya kepada pihak berwenang sesuai kebutuhan kerja.'],
            ['keamanan-perangkat', 'Keamanan Perangkat dan Pelaporan Insiden', 'A.8.1', 'Kunci layar, lindungi perangkat, dan segera laporkan insiden.'],
        ];

        foreach ($modules as $index => [$slug, $title, $reference, $description]) {
            Module::firstOrCreate(['slug' => $slug], [
                'title' => $title, 'description' => $description, 'iso_control_ref' => $reference,
                'order_level' => $index + 1, 'passing_score' => 70, 'points_reward' => 50,
                // Tetap draft hingga konten dan kuis dibuat serta ditinjau pada Fase 3.
                'is_published' => false, 'is_required' => true,
            ]);
        }
    }
}
