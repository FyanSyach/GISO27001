<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

/** Melengkapi modul dasar dengan materi teks dan kuis; publikasi tetap melalui peninjauan operator. */
class LearningContentSeeder extends Seeder
{
    /** Menambahkan materi serta tiga soal tanpa menimpa konten hasil penyuntingan. */
    public function run(): void
    {
        foreach (Module::whereIn('slug', ['dasar-keamanan-informasi', 'waspada-phishing', 'password-dan-mfa', 'perlindungan-data', 'keamanan-perangkat'])->get() as $module) {
            $module->contents()->firstOrCreate(['sort_order' => 1], ['title' => $module->title,
                'content_type' => 'text', 'body' => $module->description."\n\nPeriksa identitas pihak yang meminta informasi. Gunakan kanal resmi organisasi dan berikan akses hanya sesuai kebutuhan kerja. Jangan membagikan password atau kode MFA. Jika ragu atau terjadi insiden, hentikan aktivitas berisiko dan hubungi tim keamanan melalui kanal resmi.",
                'duration_minutes' => 3]);
            $module->contents()->firstOrCreate(['sort_order' => 2], ['title' => 'Studi kasus: permintaan mendesak',
                'content_type' => 'case_study', 'body' => 'Anda menerima pesan yang mengaku dari atasan dan meminta data rahasia segera. Jangan mengikuti tekanan waktu. Verifikasi permintaan melalui nomor atau kanal resmi yang sudah dikenal, bukan kontak dalam pesan.', 'duration_minutes' => 2]);
            foreach ([
                ['Apa langkah aman saat menerima permintaan data mendesak?', ['A' => 'Verifikasi lewat kanal resmi', 'B' => 'Segera kirim semua data', 'C' => 'Bagikan ke grup publik'], 'Verifikasi identitas dan kewenangan sebelum membagikan informasi.'],
                ['Siapa yang boleh mengetahui password dan kode MFA Anda?', ['A' => 'Jangan bagikan kepada orang lain', 'B' => 'Semua rekan kerja', 'C' => 'Pengirim pesan yang mengaku admin'], 'Password dan kode MFA merupakan rahasia autentikasi pribadi.'],
                ['Apa yang dilakukan ketika menduga terjadi insiden?', ['A' => 'Laporkan segera melalui kanal resmi', 'B' => 'Sembunyikan agar tidak disalahkan', 'C' => 'Unggah data insiden ke media sosial'], 'Pelaporan cepat membantu tim keamanan membatasi dampak insiden.'],
            ] as [$question, $options, $explanation]) {
                $module->quizzes()->firstOrCreate(['question' => $question], ['options' => $options, 'correct_answer' => 'A', 'explanation' => $explanation, 'points_reward' => 10]);
            }
        }
    }
}
