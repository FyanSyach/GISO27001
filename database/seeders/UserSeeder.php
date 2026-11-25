<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

/** Membuat akun operator dan demo tanpa mereset password atau menaikkan role akun lama. */
class UserSeeder extends Seeder
{
    /** Mengisi akun awal; kredensial eksplisit diwajibkan untuk akun baru. */
    public function run(): void
    {
        $email = config('seeding.admin_email');
        $password = config('seeding.admin_password');
        $existing = User::where('email', $email)->first();

        if ($existing && ! $existing->isAdmin()) {
            throw new RuntimeException('Email admin seed sudah dipakai akun non-admin. Pilih email lain.');
        }
        if (! $existing) {
            if (! is_string($password) || strlen($password) < 12) {
                throw new RuntimeException('SEED_ADMIN_PASSWORD minimal 12 karakter wajib diisi.');
            }
            $user = new User;
            $user->forceFill(['name' => 'Administrator GISO27001', 'username' => 'giso.admin', 'email' => $email,
                'password' => Hash::make($password), 'role' => UserRole::ADMIN, 'division' => 'Cybersecurity',
                'job_title' => 'Administrator', 'is_active' => true, 'email_verified_at' => now()])->save();
        }

        // Data pegawai fiktif hanya boleh dibuat di lingkungan pengembangan/pengujian.
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $demoPassword = config('seeding.demo_password');
        if (! is_string($demoPassword) || strlen($demoPassword) < 12) {
            throw new RuntimeException('SEED_DEMO_PASSWORD minimal 12 karakter wajib diisi untuk akun dummy.');
        }
        foreach (['HR', 'Keuangan', 'Operasional', 'Pemasaran', 'Legal'] as $index => $division) {
            for ($number = 1; $number <= 2; $number++) {
                $username = 'pegawai.'.($index + 1).'.'.$number;
                User::firstOrCreate(['email' => $username.'@giso27001.test'], [
                    'name' => 'Pegawai '.$division.' '.$number, 'division' => $division,
                    'job_title' => 'Staf', 'password' => Hash::make($demoPassword),
                ]);
            }
        }
    }
}
