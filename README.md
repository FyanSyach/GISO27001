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

25 test scaffolding auth/profil tersedia. Test memakai SQLite in-memory dan tidak membutuhkan manifest Vite. Schema domain pembelajaran, forum, gamifikasi dan seeder masuk Fase 2–5; migration fase ini hanya tabel Laravel bawaan dan atribut auth/profil pengguna. Belum ada fitur upload atau policy forum karena domain tersebut belum dibuat.

## Batas verifikasi lingkungan

PHP 8.5 dan Composer telah terpasang melalui php.new. Dependency PHP dan npm memiliki lockfile. TypeScript serta 25 test berhasil diperiksa. Build Vite dan runtime container perlu diperiksa menggunakan Node 26/Docker; Node host masih 20.5 dan Docker daemon belum aktif saat setup.
