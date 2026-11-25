# **Product Requirement Document (PRD)**

# **Platform Gamifikasi E-Learning Keamanan Informasi ISO/IEC 27001 (GISO27001)**

## **1\. Ringkasan Proyek & Tujuan**

* **Nama Proyek:** Gamified E-Learning ISO/IEC 27001 Awareness Platform  
* **Target Pengguna Utama:** Pegawai khususnya Non-IT (Rentang umur: 20–58 tahun)  
* **Tujuan Utama:**  
  1. Meningkatkan kesadaran (*awareness*) keamanan informasi standar ISO/IEC 27001 menggunakan pendekatan pembelajaran mikro (*micro-learning*) dan studi kasus nyata.  
  2. Mendorong konsistensi belajar melalui elemen gamifikasi (Poin, Badge, Level, Leaderboard, dan Streak).  
  3. Menyediakan portal interaktif sosial (Forum Diskusi) serta fitur pelaporan & analitik bagi Admin/Manajemen.  
* **Prinsip Pengodean & Arsitektur:**  
  * Menggunakan arsitektur modern Monolith / Hybrid SPA-feel Frontend dengan Inertia.js \+ React 19+ (TypeScript) dan Tailwind CSS v4.  
  * Memanfaatkan PHP 8.5+ native attributes, Laravel 13 Typed Eloquent Models, dan penyusunan fungsi berbasis DocBlock/Inline comments berbahasa Indonesia pada setiap layer controller, model, middleware, policy, dan service.
  * **Kewajiban dokumentasi kode:** Setiap file **controller, model, middleware, dan view** wajib memiliki komentar dokumentasi yang jelas mengenai tanggung jawab serta konteksnya. Setiap fungsi/method wajib disertai **DocBlock** yang menjelaskan tujuan, parameter, nilai balik, dan efek samping penting; gunakan **inline comment** berbahasa Indonesia untuk menjelaskan logika yang tidak langsung terlihat. Dokumentasi ini wajib dijadikan standar pada seluruh implementasi fase berikutnya.

## **2\. Tech Stack & Engine Requirement**

| Komponen Engine | Teknologi & Versi Spesifik | Catatan Arsitektur & Implementasi |
| :---- | :---- | :---- |
| **Backend Engine** | Laravel 13.x | Core framework, Auth, Eloquent Attributes, Form Request Validation, Policy Authorization, Notification Channel |
| **PHP Runtime** | PHP 8.5.x | Native Attributes, Pipe Operator (\|\>), Typed Eloquent Properties, Strict Type Hints |
| **Node.js Runtime** | Node.js 26.13.x (LTS) | Build toolchain & asset compilation runtime \- Vite 6+, Tailwind CSS v4 / PostCSS |
| **Database Engine** | MySQL 8.4 (LTS) | Relational DB dengan JSON Column support, Indexed foreign keys, Pessimistic Locking (`lockForUpdate`), Window Functions |
| **Frontend Stack & UI Framework** | Inertia.js \+ React 19+ (TypeScript) / Tailwind v4 | Fully Responsive UI \- Single Page Application (SPA) feel, Mobile-First, Tablet, Desktop, Micro-interactions, Dark Mode Support |
| **Media & Storage** | Local / S3 Driver | Penyimpanan infografis, video subtitle VTT, sertifikat PDF, dan avatar pengguna |

## **3\. Spesifikasi Fitur Fungsional**

### **3.1 Manajemen Pengguna, Otentikasi & Keamanan Aplikasi**

* **Registrasi & Login:** Login berbasis Username/Email \+ Password dengan proteksi enkripsi dan Rate Limiting (maksimal 5 percobaan per menit per IP/akun).  
* **Profil Pengguna:** Nama lengkap, Divisi/Unit Kerja (`division`), Jabatan (`job_title`), Foto Profil (`avatar`), Skor Poin, Level, Streak Aktif, dan Status Akun (`is_active`).  
* **Role & Akses (RBAC & Policies):** Role `USER` (Karyawan non-IT) dan `ADMIN` (HR/Training/Cybersecurity Team), dilengkapi Laravel Policy pada Forum (user hanya bisa mengedit/menghapus thread atau balasan miliknya sendiri) dan Admin Panel.  
* **Logout, Sesi & Validasi File:** Manajemen sesi aman menggunakan middleware `PreventRequestForgery` Laravel 13 serta validasi ketat MIME type dan ukuran maksimal file unggahan (avatar maks. 2MB gambar; materi/infografis maks. 10MB).

### **3.2 Manajemen Konten Pembelajaran Mikro (Micro-Learning)**

* **Modul Belajar (5–10 Modul Wajib):** Penyusunan materi berjenjang sesuai kontrol ISO/IEC 27001 (`order_level` berurutan).  
* **Konten Mikro (2–10 Menit/Sesi):** Setiap modul memiliki unit konten mikro (`module_contents`) berformat video animasi (dengan subtitle VTT), infografis visual, dan narasi teks bahasa non-teknis.  
* **Studi Kasus Skenario Nyata:** Simulasi skenario harian (misal: Phishing Email, Password Leak, Keamanan Laptop & Media Sosial).  
* **Kuis / Assessment Interaktif:** 3–5 pertanyaan pilihan ganda/skenario di setiap modul dengan batas nilai kelulusan (passing grade minimal 70%).  
* **Sertifikat Digital Auto-Generated:** Downloadable PDF sertifikat unik (dilengkapi nomor seri/kode verifikasi) setelah menuntaskan 100% modul wajib dan lulus seluruh kuis.  
* **Pencarian Konten:** Search engine materi internal berbasis judul, kategori ISO 27001, dan kata kunci untuk Admin & Pengguna.

### **3.3 Engine Gamifikasi (Gamification Engine)**

* **Poin & Anti-Farming System:**  
  * Penyelesaian modul pertama kali: \+50 poin (tidak dapat diklaim ulang kecuali di-reset Admin).  
  * Penyelesaian kuis: \+10 hingga \+50 poin sesuai skor; poin hanya diberikan pada percobaan pertama yang mencapai passing grade (pengulangan berikutnya hanya untuk latihan tanpa tambahan poin).  
  * Bonus Daily Streak: \+10 poin per hari aktif beruntun.  
* **Badges / Lencana Pencapaian:** Sistem atribusi lencana otomatis berbasis kriteria poin maupun penyelesaian modul tertentu (contoh: *Guardian of Data*, *Security Champion*, *Fast Learner*).  
* **Sistem Level:** Progress bar tingkatan level (Level 1–10) berdasarkan kriteria akumulasi poin (`floor(points / 100) + 1`, maksimal Level 10).  
* **Leaderboard Interaktif:** Pemeringkatan real-time berbasis Individu dan Total/Rata-rata Skor per Divisi (`division`), hanya menampilkan user berstatus aktif (`is_active = true`).  
* **Streak Tracking:** Perhitungan hari beruntun pengguna aktif menyelesaikan sesi belajar berdasarkan `last_active_date` (reset ke 1 jika melewatkan lebih dari 1 hari kalender).  
* **Daily / Weekly Challenge:** Tantangan berkala (`challenges`) dengan batas waktu aktif dan klaim reward otomatis sekali per tantangan (`user_challenges`).

### **3.4 Fitur Sosial & Interaktif**

* **Forum Diskusi Keamanan:** Pengguna dapat membuat thread pertanyaan/topik, membalas diskusi, serta menandai solusi terbaik (*best answer*) pada thread miliknya.  
* **Sharing Pencapaian:** Fitur membagikan kartu pencapaian Badge/Sertifikat ke jejaring internal atau tautan verifikasi publik.

### **3.5 Panel Admin & Analitik Manajemen**

* **Dashboard Admin:** Ringkasan total user aktif, tingkat penyelesaian modul (*completion rate*), rata-rata skor kuis, dan peringkat divisi terbaik.  
* **CRUD Konten & Gamifikasi:** Pengelolaan modul, unit konten mikro, bank soal kuis, aturan poin/badge, tantangan harian/mingguan, dan kategori forum.  
* **Kelola Pengguna:** Audit daftar karyawan, pengaturan ulang (reset) progress, perubahan divisi/role, atau penonaktifan akun (`is_active = false`).  
* **Laporan & Export Data:** Export laporan statistik (Excel/PDF) per divisi dan riwayat individu untuk kebutuhan audit kepatuhan ISO/IEC 27001\.  
* **Notifikasi Massal (Broadcasting):** Pengiriman pengumuman jadwal pelatihan atau modul baru via in-app notification & email.

### **3.6 Kebijakan & Kewajiban Version Locking**

* **Penggunaan Versi Terbaru / LTS:** Seluruh dependency, package, dan framework wajib menggunakan versi terbaru atau versi Long-Term Support (LTS) stabil (PHP 8.5.x, Laravel 13.x, Node.js 26.13.x LTS, MySQL 8.4 LTS, React 19+, Tailwind CSS v4).  
* **Larangan Downgrade:** Dilarang menurunkan (downgrade) versi dependency utama demi mempertahankan standar keamanan dan performa terbaik.  
* **Verifikasi Keamanan Dependency:** Wajib menjalankan pemeriksaan kerentanan berkala menggunakan tool audit dependency (`composer audit` dan `npm audit`).

### **3.7 Infrastruktur & Containerization (Docker)**

* **Isolasi Lingkungan (Environment Isolation):** Menggunakan `Dockerfile` dan `docker-compose.yml` untuk menjamin konsistensi lingkungan dari development hingga production.  
* **Layanan Service Container:** Kontainer terpisah untuk Web Server (Nginx), Backend App (PHP 8.5-FPM), Database (MySQL 8.4 LTS), Caching/Queue (Redis), dan Asset Compilation (Node.js/Vite Dev Server).

### **3.8 Manajemen Source Code & CI/CD (GitHub)**

* **Strategi Percabangan (Git Branching Strategy):** Mengadopsi workflow `main` (production-ready), `develop` (staging/integration), dan branch fitur (`feature/*`).  
* **Code Review & Pull Request:** Setiap penggabungan kode wajib melalui mekanisme Pull Request (PR) dengan minimal 1 persetujuan peer reviewer.  
* **Otomatisasi CI/CD (GitHub Actions):** Workflow otomatis mencakup pengujian otomatis (Pest/PHPUnit), static analysis/linter (Pint/ESLint), serta pemindaian keamanan (`composer audit` & `npm audit`) pada setiap push dan PR.

## **4\. Struktur Database (MySQL 8.4 Migrate Schemas)**

Platform menggunakan skema database relasional terintegrasi berikut agar konsisten dengan seluruh fitur fungsional:

1. **`users`**: `id` (PK), `name`, `email` (unique), `password`, `division`, `job_title`, `avatar` (nullable), `role` (enum: `user`, `admin`), `points` (default 0), `level` (default 1), `current_streak` (default 0), `last_active_date` (date, nullable), `is_active` (boolean, default true), `timestamps`.  
2. **`modules`**: `id` (PK), `title`, `slug` (unique), `description`, `iso_control_ref` (string, nullable), `order_level` (int), `passing_score` (int, default 70), `points_reward` (int, default 50), `is_published` (boolean), `timestamps`.  
3. **`module_contents`**: `id` (PK), `module_id` (FK \-\> modules), `title`, `content_type` (enum: `video`, `infographic`, `text`, `case_study`), `body` (text, nullable), `media_url` (string, nullable), `subtitle_url` (string, nullable), `duration_minutes` (int), `sort_order` (int), `timestamps`.  
4. **`user_progress`**: `id` (PK), `user_id` (FK \-\> users), `module_id` (FK \-\> modules), `is_completed` (boolean), `points_awarded` (boolean, default false), `completed_at` (timestamp, nullable), `timestamps` (unique constraint: `user_id`, `module_id`).  
5. **`quizzes`**: `id` (PK), `module_id` (FK \-\> modules), `question`, `options` (JSON), `correct_answer`, `explanation` (text, nullable), `points_reward` (int), `timestamps`.  
6. **`quiz_attempts`**: `id` (PK), `user_id` (FK \-\> users), `quiz_id` (FK \-\> quizzes), `selected_answer`, `is_correct` (boolean), `attempt_number` (int), `points_earned` (int, default 0), `timestamps`.  
7. **`badges`**: `id` (PK), `code` (unique), `name`, `description`, `icon_path`, `category` (enum), `min_points` (int, nullable), `required_module_id` (FK \-\> modules, nullable), `timestamps`.  
8. **`user_badges`**: `id` (PK), `user_id` (FK \-\> users), `badge_id` (FK \-\> badges), `earned_at` (timestamp), `timestamps` (unique constraint: `user_id`, `badge_id`).  
9. **`challenges`**: `id` (PK), `title`, `description`, `frequency` (enum: `daily`, `weekly`), `target_type` (string), `target_count` (int), `points_reward` (int), `starts_at` (timestamp), `ends_at` (timestamp), `timestamps`.  
10. **`user_challenges`**: `id` (PK), `user_id` (FK \-\> users), `challenge_id` (FK \-\> challenges), `progress_count` (int, default 0), `is_completed` (boolean, default false), `completed_at` (timestamp, nullable), `timestamps` (unique constraint: `user_id`, `challenge_id`).  
11. **`certificates`**: `id` (PK), `user_id` (FK \-\> users), `certificate_number` (unique), `file_path`, `issued_at` (timestamp), `timestamps`.  
12. **`forum_threads`**: `id` (PK), `user_id` (FK \-\> users), `category` (string), `title`, `content` (text), `best_reply_id` (unsignedBigInteger, nullable), `is_locked` (boolean, default false), `timestamps`.  
13. **`forum_replies`**: `id` (PK), `thread_id` (FK \-\> forum\_threads), `user_id` (FK \-\> users), `content` (text), `timestamps`.

## **5\. Struktur Proyek & Arsitektur Kode (Laravel 13 & PHP 8.5)**

Struktur direktori repositori mengikuti konvensi Laravel 13 dan Inertia.js \+ React 19+ (TypeScript):t  
gamified-elearning-iso27001/  
├── app/  
│   ├── Enums/  
│   │   ├── UserRole.php  
│   │   ├── BadgeCategory.php  
│   │   └── ContentType.php  
│   ├── Http/  
│   │   ├── Controllers/  
│   │   │   ├── Admin/  
│   │   │   │   ├── DashboardController.php  
│   │   │   │   ├── ModuleManagementController.php  
│   │   │   │   └── UserManagementController.php  
│   │   │   ├── Auth/  
│   │   │   ├── LearningModuleController.php  
│   │   │   ├── QuizController.php  
│   │   │   ├── LeaderboardController.php  
│   │   │   ├── CertificateController.php  
│   │   │   └── ForumController.php  
│   │   ├── Middleware/  
│   │   │   └── EnsureUserHasRole.php  
│   │   └── Requests/  
│   │       ├── SubmitQuizRequest.php  
│   │       └── StoreThreadRequest.php  
│   ├── Models/  
│   │   ├── User.php  
│   │   ├── Module.php  
│   │   ├── ModuleContent.php  
│   │   ├── Quiz.php  
│   │   ├── QuizAttempt.php  
│   │   ├── UserProgress.php  
│   │   ├── Badge.php  
│   │   ├── Challenge.php  
│   │   ├── Certificate.php  
│   │   ├── ForumThread.php  
│   │   └── ForumReply.php  
│   ├── Policies/  
│   │   └── ForumThreadPolicy.php  
│   └── Services/  
│       ├── GamificationService.php  
│       ├── CertificateService.php  
│       └── StreakTrackerService.php  
├── database/  
│   ├── factories/  
│   ├── migrations/  
│   └── seeders/  
├── docker/  
│   ├── nginx/  
│   └── php/  
├── resources/  
│   ├── js/  
│   │   ├── Components/  
│   │   ├── Layouts/  
│   │   ├── Pages/  
│   │   │   ├── Admin/  
│   │   │   ├── Forum/  
│   │   │   ├── Leaderboard/  
│   │   │   └── Modules/  
│   │   ├── types/  
│   │   └── app.tsx  
│   └── css/  
│       └── app.css  
├── routes/  
│   ├── web.php  
│   └── api.php  
├── .github/  
│   └── workflows/  
│       └── ci-cd.yml  
├── docker-compose.yml  
├── Dockerfile  
└── tests/  
├── Feature/  
└── Unit/

````

### 6. Exemplar Kode Berstandar DocBlock & Concurrency Safety (PHP 8.5 & Laravel 13)

#### 6.1 Model: `User.php`

```php
<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class User
 *
 * Memetakan entitas pengguna/karyawan pada platform e-learning.
 * Mengelola informasi profil, divisi, akumulasi poin gamifikasi, level, dan streak.
 *
 * @package App\Models
 * @property int $id Identifier utama pengguna
 * @property string $name Nama lengkap pegawai
 * @property string $email Alamat email resmi organisasi
 * @property string $password Hashed password
 * @property string $division Divisi atau unit kerja
 * @property string|null $job_title Jabatan pegawai
 * @property string|null $avatar Path foto profil pengguna
 * @property UserRole $role Peran akses pengguna (user/admin)
 * @property int $points Total perolehan poin gamifikasi
 * @property int $level Tingkatan level pengguna saat ini
 * @property int $current_streak Jumlah hari aktif belajar secara berurutan
 * @property \Illuminate\Support\Carbon|null $last_active_date Tanggal aktivitas belajar terakhir
 * @property bool $is_active Status keaktifan akun pegawai
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'division',
        'job_title',
        'avatar',
        'role',
        'points',
        'level',
        'current_streak',
        'last_active_date',
        'is_active',
    ];

    /**
     * Atribut yang di-cast ke tipe data spesifik PHP 8.5.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'points' => 'integer',
            'level' => 'integer',
            'current_streak' => 'integer',
            'last_active_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke data lencana (badges) yang telah diraih pengguna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }

    /**
     * Relasi ke progres penyelesaian modul e-learning pengguna.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }
}
````

### **6.2 Service: `GamificationService.php`**

```
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use Illuminate\Support\Facades\DB;

/**
 * Class GamificationService
 *
 * Layanan bisnis utama untuk mengkalkulasi insentif poin, pembaharuan level,
 * serta evaluasi kelayakan perolehan lencana pencapaian (badges) secara aman dari race condition.
 *
 * @package App\Services
 */
class GamificationService
{
    /**
     * Menambahkan poin ke akun pengguna menggunakan pessimistic locking dan PHP 8.5 Pipe Operator.
     *
     * @param User $user Pengguna yang menerima poin
     * @param int $earnedPoints Jumlah poin yang ditambahkan
     * @return User State pengguna terbaru setelah diperbarui
     */
    public function grantPoints(User $user, int $earnedPoints): User
    {
        return DB::transaction(function () use ($user, $earnedPoints): User {
            /** @var User $lockedUser */
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedUser->points += max(0, $earnedPoints);
            $lockedUser->level = $lockedUser->points
                |> (static fn (int $pts): int => (int) floor($pts / 100) + 1)
                |> (static fn (int $lvl): int => min($lvl, 10));

            $lockedUser->save();

            $this->checkAndAwardBadges($lockedUser);

            return $lockedUser;
        });
    }

    /**
     * Memeriksa kriteria penyelesaian dan mengatribusikan lencana baru jika memenuhi syarat.
     *
     * @param User $user
     * @return void
     */
    protected function checkAndAwardBadges(User $user): void
    {
        $ownedBadgeIds = $user->badges()->pluck('badges.id');

        $eligibleBadges = Badge::query()
            ->whereNotNull('min_points')
            ->where('min_points', '<=', $user->points)
            ->whereNotIn('id', $ownedBadgeIds)
            ->get();

        foreach ($eligibleBadges as $badge) {
            $user->badges()->syncWithoutDetaching([
                $badge->id => ['earned_at' => now()],
            ]);
        }
    }
}
```

## **7\. Desain Responsif & Aksesibilitas Layar (Responsive Breakpoints)**

Antarmuka dikembangkan dengan pendekatan **Mobile-First** untuk memastikan kenyamanan akses pegawai lintas perangkat:

* **Layar Mobile (\< 640px):**  
  * Layout *Single-Column* sederhana.  
  * Navigasi utama menggunakan *Bottom Navigation Bar* tetap (*fixed*).  
  * Area sentuh (*touch target*) tombol minimal 48px × 48px untuk aksesibilitas jempol.  
* **Layar Tablet (640px – 1024px):**  
  * Grid 2-kolom untuk daftar modul kuis dan statistik.  
  * Navigasi samping (*collapsible sidebar*) yang dapat disembunyikan.  
* **Layar Desktop (\> 1024px):**  
  * Multi-kolom penuh (3 atau 4 kolom) menampilkan konten pembelajaran, panel progres harian, serta leaderboard *real-time* secara berdampingan.

## **8\. Urutan Eksekusi Pembangunan (AI Agent Build Order)**

Untuk menjaga konsistensi eksekusi bertahap oleh AI coding agent (OpenCode):

1. **Fase 1 – Infrastruktur & Keamanan Dasar:** Setup `Dockerfile` \+ `docker-compose.yml` (Nginx, PHP 8.5-FPM, MySQL 8.4, Redis, Node 26), konfigurasi Laravel 13 \+ Inertia React 19 \+ Tailwind v4, serta sistem Auth & RBAC (`UserRole`, `EnsureUserHasRole`).  
2. **Fase 2 – Skema Database & Seeder:** Buat seluruh file migration (13 tabel utama) beserta Factory dan Seeder awal (akun Admin, akun User dummy lintas divisi, 5 modul dasar ISO/IEC 27001, dan master Badges).  
3. **Fase 3 – Modul Micro-Learning & Kuis:** Implementasi tampilan modul berjenjang, pemutar konten mikro (`module_contents`), pengerjaan kuis beserta validasi anti-farming, dan pencatatan `user_progress`.  
4. **Fase 4 – Engine Gamifikasi & Sertifikat:** Implementasi `GamificationService` (dengan `lockForUpdate`), `StreakTrackerService`, `Challenge` harian/mingguan, Leaderboard Individu & Divisi, serta generator PDF `CertificateService`.  
5. **Fase 5 – Forum Diskusi & Panel Admin:** Implementasi Forum Diskusi \+ `ForumThreadPolicy`, Dashboard Analitik Admin, manajemen CRUD konten/user, export laporan Excel/PDF, dan pengujian otomatis (Pest).

## **9\. Panduan Pengujian & Kriteria Penerimaan (Acceptance Criteria)**

1. **Runtime & Ekosistem:** Seluruh lingkungan aplikasi berjalan stabil tanpa error peringatan deprecation pada PHP 8.5.x, Laravel 13.x, Node.js 26.13.x, dan MySQL 8.4 (LTS).  
2. **Dokumentasi Kode:** Seluruh file Model, Controller, Request, Policy, dan Service dilengkapi DocBlock penjelasan peran berbahasa Indonesia.  
3. **Akurasi & Integritas Gamifikasi:** Penambahan poin kuis (anti-farming), penentuan *streak* harian berurutan, dan otomatisasi klaim lencana berjalan presisi tanpa *race condition*.  
4. **Ekspor Sertifikat:** Generasi file PDF sertifikat ISO/IEC 27001 berfungsi sempurna setelah progres modul dan kelulusan kuis mencapai 100%.  
5. **Aksesibilitas & UI:** Antarmuka responsif penuh tanpa *horizontal overflow* pada resolusi mobile 360px hingga desktop 4K.

