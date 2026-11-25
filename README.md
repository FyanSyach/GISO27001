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

## Batas verifikasi lingkungan

PHP 8.5 dan Composer telah terpasang melalui php.new. Dependency PHP dan npm memiliki lockfile. Suite lokal Fase 2 sebelumnya lulus 29 test/86 assertions. Build Vite dan smoke test runtime Docker sudah berhasil (lihat hasil Fase 2 di atas); seluruh suite PHPUnit belum dijalankan di dalam container. Node host masih 20.5, sehingga gunakan container Node 26 untuk build.

Stack verifikasi menggunakan `.env.verify` (diabaikan Git, berisi secret acak), override `docker/compose.verify.yml`, project `giso-verify`, dan volume MySQL terpisah. `.env` lokal tidak diubah. Akses verifikasi: http://localhost:18080. Node dev server dihentikan agar memakai hasil build statis. Untuk menghentikan stack tanpa menghapus data:

```sh
docker compose --env-file .env.verify -f docker-compose.yml -f docker/compose.verify.yml -p giso-verify down
```
