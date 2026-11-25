# GISO27001 — Fase 1

Fondasi Laravel 13, PHP 8.5, Inertia 2, React 19 (TypeScript), Tailwind CSS 4, MySQL 8.4, Redis dan Node 26.

## Menjalankan dengan Docker (development)

Aktifkan Docker Desktop dengan Linux containers. Buka ulang terminal setelah instalasi PHP/Composer agar PATH terbaru berlaku.

```powershell
Copy-Item .env.docker.example .env
docker compose build app
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose up -d mysql redis
docker compose run --rm app php artisan migrate
docker compose up -d
```

Buka http://localhost:8080. Node menjalankan Vite HMR pada port 5173. Untuk build statis:

```sh
docker compose run --rm node npm run build
docker compose stop node
```

Jika terjadi permission denied pada storage/bootstrap/cache di Linux, sesuaikan ownership untuk user `www-data` di container. Ini stack development dengan bind mounts, bukan deployment produksi. Untuk produksi gunakan TLS, `APP_DEBUG=false`, secret unik, cookie secure, image immutable dan jangan publish port MySQL.

## Auth & RBAC

- Breeze menyediakan registrasi, login, logout, reset password, konfirmasi password, verifikasi email dan profil.
- Login menerima email atau username melalui field `email`. Username opsional dan unik di database; pengelolaannya belum tersedia di UI.
- Password di-hash; login gagal dibatasi 5 percobaan/menit per kombinasi identitas dan IP.
- Akun nonaktif ditolak pada login dan sesi berjalan dihentikan.
- Registrasi publik selalu memakai default role `user`; input role tidak diterima.
- `UserRole::USER` / `UserRole::ADMIN` menggunakan nilai database `user` / `admin`.
- `/admin` dibatasi middleware `auth` dan `role:admin`; halaman masih placeholder dashboard.
- Laravel web middleware tetap mengaktifkan proteksi request forgery bawaan. Logout menginvalidasi sesi dan meregenerasi CSRF token.
- Belum ada akun admin/password bawaan. Ubah role akun yang telah didaftarkan melalui Tinker hanya oleh operator tepercaya:

```sh
docker compose exec app php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@example.com')->firstOrFail();
$user->role = App\Enums\UserRole::ADMIN;
$user->save();
```

Mail lokal memakai log. Konfigurasikan SMTP sebelum memakai reset password pada lingkungan nyata.

## Pemeriksaan

```sh
docker compose exec app php artisan test
docker compose exec app vendor/bin/pint --test
docker compose run --rm node npm run typecheck
docker compose config --quiet
```

Test auth/profil dan database Fase 2 memakai SQLite in-memory dan tidak membutuhkan manifest Vite. Skema domain tersedia pada Fase 2; fitur pembelajaran, gamifikasi, upload dan policy forum belum diimplementasikan (Fase 3–5).

## Fase 2 — Skema dan data awal

12 migration domain melengkapi tabel `users` menjadi 13 tabel utama PRD (di luar tabel infrastruktur Laravel). Factory tersedia untuk seluruh entitas. Constraint unik melindungi progres, badge pengguna, klaim challenge, dan nomor percobaan kuis dari duplikasi. `best_reply_id` disiapkan sebagai kolom nullable tanpa foreign key sirkular; validasi bahwa solusi berasal dari thread yang sama wajib diimplementasikan pada Fase 5.

Isi `.env` dengan `SEED_ADMIN_EMAIL`, `SEED_ADMIN_PASSWORD`, dan `SEED_DEMO_PASSWORD` (password minimal 12 karakter), kemudian:

```sh
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

Seeder membuat 1 admin, 10 pegawai dummy dari 5 divisi (hanya local/testing), 5 modul draft, dan 3 badge. Tidak ada password bawaan. Email dummy berupa `pegawai.1.1@giso27001.test` hingga `pegawai.5.2@giso27001.test`. Akun admin memiliki username `giso.admin`; pengguna dummy login melalui email. Seed ulang mempertahankan data yang sudah ada, termasuk password; bukan alat reset akun. Jangan menjalankan `migrate:fresh` pada database berisi data penting.

Kategori badge diputuskan menjadi `points`, `module`, dan `achievement` karena PRD belum merinci nilai enum. Fast Learner menggunakan kriteria penyelesaian modul dasar, bukan waktu penyelesaian yang belum didefinisikan PRD. Referensi kontrol dan materi awal perlu ditinjau pemilik konten sebelum publikasi. Factory sertifikat hanya menyediakan path data uji, bukan file PDF aktual.

Test otomatis Fase 2 menggunakan SQLite in-memory. Verifikasi Docker pada 25 November 2026 berhasil: PHP 8.5.13, MySQL 8.4.13, Node 26.13.1; seluruh migration diterapkan, seed dua kali tetap menghasilkan 11 pengguna, 5 modul, dan 3 badge. Redis merespons PONG; Nginx mengembalikan HTTP 200 untuk `/`, `/login`, dan `/up`. Build produksi frontend beserta TypeScript berhasil. Ini smoke test infrastruktur, bukan pengujian end-to-end login atau seluruh constraint MySQL. Branch kerja: `feature/phase-2-database-seeders`; penggabungan ke `develop`/`main` mengikuti PR dan persetujuan reviewer sesuai PRD.

## Fase 3 — Pembelajaran dan kuis

Branch `feature/phase-3-learning-quizzes` menyediakan `/modules` dan `/modules/{slug}` untuk pengguna login. Modul terbit diurutkan berdasarkan `order_level`; seluruh modul terbit sebelumnya wajib selesai. Modul draft tidak dapat dibuka dengan URL langsung.

Konten teks/studi kasus dirender sebagai teks (bukan HTML mentah), video memakai pemutar HTML5 dengan subtitle VTT, dan infografis memakai gambar responsif. URL media hanya menerima HTTP(S) atau path lokal. Media video/infografis tidak disertakan dalam seed; gunakan media milik organisasi yang telah ditinjau.

`LearningContentSeeder` menambahkan dua konten dan tiga soal contoh pada setiap modul dasar tanpa mengubah status draft. Materi contoh bersifat umum, belum menggantikan kurasi modul spesifik oleh pemilik konten. Jalankan `php artisan db:seed --class=LearningContentSeeder` setelah master modul tersedia. Publikasikan hanya setelah isi dan jawaban diperiksa operator; UI pengelolaan konten ada pada Fase 5.

Penyelesaian modul ditandai ketika kuis lulus (bukan sekadar membuka halaman atau klaim durasi menonton). Jawaban harus tepat mencakup seluruh pertanyaan modul, dengan opsi valid. Skor dihitung server menggunakan persentase jawaban benar, dibulatkan ke bawah. Pada kelulusan pertama diberikan reward modul dan reward kuis proporsional skor (10–50); semua percobaan setelah lulus adalah latihan tanpa reward. Satu transaksi mengunci record pengguna, menyimpan percobaan, memperbarui progres dan poin/level. Reward kuis total dicatat di baris jawaban pertama agar agregasi `points_earned` tidak menggandakan total. Ini fondasi sementara; integrasi badge/streak dan penyatuan reward pada GamificationService masuk Fase 4.

Suite lokal: 33 test/116 assertions lulus, mencakup akses, anti-farming, dan penyembunyian kunci jawaban. Build Node 26/TypeScript berhasil. Belum dilakukan pengujian konkurensi MySQL atau pemeriksaan visual lintas perangkat. Pemutar media disediakan, tetapi bukan sistem pembuktian durasi menonton atau pelacakan progres per konten.

## Fase 4 — Gamifikasi dan sertifikat

Halaman `/achievements` menampilkan 50 peringkat individu teratas, total/rata-rata poin divisi (hanya akun aktif), serta progres challenge aktif. Ranking dapat diperbarui melalui tombol `Perbarui ranking` saat diperlukan; implementasi tidak bergantung pada WebSocket sehingga tetap sederhana dan kompatibel dengan deployment standar. Tautan tersedia dari katalog modul.

`GamificationService` mengunci pengguna dengan `lockForUpdate`, mengatur level maksimal 10 dan mengatribusikan badge kategori poin/modul sekali. Pemanggil bertanggung jawab atas idempotensi event: kelulusan memakai `points_awarded`, streak memakai `last_active_date`, dan challenge memakai `is_completed`. Tidak tersedia endpoint publik untuk menambah poin atau mengirim progres challenge manual.

Streak berdasarkan timezone aplikasi (`config/app.php`); kuis lulus dihitung sebagai aktivitas belajar, termasuk latihan hari berikutnya. Bonus 10 poin diberikan sekali per hari; jeda lebih dari satu hari mereset streak menjadi 1. Total poin pada hasil kuis masih hanya menampilkan reward modul/kuis, tidak termasuk bonus streak/challenge yang terlihat melalui leaderboard.

Challenge harian membutuhkan satu modul baru, mingguan tiga modul baru, dengan reward 10/30 poin. Hanya penyelesaian pertama dalam periode aktif yang dihitung. Jalankan `php artisan db:seed --class=ChallengeSeeder` untuk periode sekarang. Scheduler harian pada `routes/console.php` menyiapkan periode berikutnya; operator wajib menjalankan `php artisan schedule:work` untuk development atau cron `schedule:run` untuk produksi.

`/certificate` menghasilkan PDF privat menggunakan Dompdf, hanya untuk pengguna login yang telah menyelesaikan dan lulus seluruh modul yang ditandai `is_required`. Flag ini tersedia pada migration modul dan dapat dikelola admin pada fase panel admin. Bukti kelulusan diperiksa dari percobaan kuis, bukan hanya flag progres. Sertifikat memakai nomor UUID unik dan diunduh melalui server, bukan URL storage publik. Nomor verifikasi tercetak pada PDF dan tersedia melalui halaman publik `/certificate/verify/{certificateNumber}` dengan data minimum untuk validasi. Dokumen adalah sertifikat pembelajaran internal, bukan sertifikasi ISO organisasi. Sertifikat lama tidak diregenerasi otomatis ketika nama atau katalog berubah. File dan transaksi database tidak atomik lintas filesystem; kegagalan commit setelah penulisan berpotensi meninggalkan file orphan.

Verifikasi Fase 4: 38 test/143 assertions lulus, PDF aktual berawalan `%PDF-` berhasil dibuat dalam test, build Node 26/TypeScript berhasil, dan `composer audit` tanpa advisory. Belum dilakukan stress test konkurensi MySQL, pengujian visual PDF/browser, atau pengujian runtime Dompdf di container PHP. Tidak ada commit/push otomatis.

## Batas verifikasi lingkungan

PHP 8.5 dan Composer telah terpasang melalui php.new. Dependency PHP dan npm memiliki lockfile. Suite lokal Fase 2 sebelumnya lulus 29 test/86 assertions. Build Vite dan smoke test runtime Docker sudah berhasil (lihat hasil Fase 2 di atas); seluruh suite PHPUnit belum dijalankan di dalam container. Node host masih 20.5, sehingga gunakan container Node 26 untuk build.

Stack verifikasi menggunakan `.env.verify` (diabaikan Git, berisi secret acak), override `docker/compose.verify.yml`, project `giso-verify`, dan volume MySQL terpisah. `.env` lokal tidak diubah. Akses verifikasi: http://localhost:18080. Node dev server dihentikan agar memakai hasil build statis. Untuk menghentikan stack tanpa menghapus data:

```sh
docker compose --env-file .env.verify -f docker-compose.yml -f docker/compose.verify.yml -p giso-verify down
```
