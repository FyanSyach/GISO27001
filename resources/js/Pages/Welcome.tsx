import BrandLogo from '@/Components/BrandLogo';
import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';

/** Keunggulan singkat yang menjelaskan cara belajar di GISO27001. */
const benefits = [
    {
        title: 'Mikro, bukan maraton',
        description: 'Setiap sesi dirancang 2–10 menit agar mudah disisipkan di sela pekerjaan.',
        icon: 'M12 7v5l3 2m-3-11a9 9 0 1 1 0 18 9 9 0 0 1 0-18Z',
    },
    {
        title: 'Skenario dunia nyata',
        description: 'Latihan menghadapi phishing, password bocor, dan permintaan data mendesak.',
        icon: 'M12 3 4 6.5v5.2c0 4.6 3.3 8.7 8 9.8 4.7-1.1 8-5.2 8-9.8V6.5L12 3Zm-2.6 9.1 1.9 1.9 3.7-4',
    },
    {
        title: 'Progres yang terlihat',
        description: 'Poin, level, badge, dan streak membantu menjaga kebiasaan belajar tetap konsisten.',
        icon: 'M7 20V10m5 10V4m5 16v-7m5 7V7',
    },
    {
        title: 'Aman untuk semua divisi',
        description: 'Bahasa non-teknis, kontras tinggi, dan navigasi ramah pengguna di ponsel.',
        icon: 'M4 7h16v10H4V7Zm4 13h8M9 17v3m6-3v3',
    },
];

/** Langkah belajar yang ditampilkan sebagai alur tiga tahap. */
const steps = [
    ['Pahami', 'Baca materi singkat dan studi kasus yang dekat dengan pekerjaan harian.'],
    ['Uji', 'Kerjakan kuis singkat. Nilai kelulusan ditentukan tiap modul, minimal 70%.'],
    ['Kumpulkan', 'Dapatkan poin, buka modul berikutnya, dan raih sertifikat awareness.'],
];

/** Kartu pratinjau modul; hanya ilustrasi statis, bukan data dari basis data. */
const previewModules = [
    { level: 1, title: 'Dasar Keamanan Informasi', control: 'A.6.3', minutes: 5, status: 'selesai' },
    { level: 2, title: 'Waspada Phishing', control: 'A.6.3', minutes: 4, status: 'aktif' },
    { level: 3, title: 'Password Kuat & MFA', control: 'A.5.17', minutes: 6, status: 'terkunci' },
];

/** Halaman publik GISO27001 dengan ajakan masuk untuk pengguna dan pendaftaran akun. */
export default function Welcome({ auth, canLogin, canRegister }: PageProps<{ canLogin: boolean; canRegister: boolean }>) {
    const primaryHref = auth.user ? route('modules.index') : canLogin ? route('login') : '#';

    return (
        <>
            <Head title="Belajar Keamanan Informasi" />

            <div className="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
                <a
                    href="#konten-utama"
                    className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-slate-900 focus:shadow"
                >
                    Lewati ke konten utama
                </a>

                <header className="sticky top-0 z-40 border-b border-slate-200/80 bg-slate-50/85 backdrop-blur-md dark:border-white/10 dark:bg-slate-950/80">
                    <div className="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                        <Link href="/" className="flex items-center gap-3 rounded-lg">
                            <BrandLogo />
                            <span className="flex flex-col leading-tight">
                                <span className="text-base font-bold tracking-tight sm:text-lg">GISO27001</span>
                                <span className="text-[11px] font-medium uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
                                    Awareness Platform
                                </span>
                            </span>
                        </Link>

                        <nav aria-label="Navigasi utama" className="hidden items-center gap-1 md:flex">
                            {[
                                ['#manfaat', 'Manfaat'],
                                ['#alur', 'Cara belajar'],
                                ['#modul', 'Modul'],
                                ['#keamanan', 'Keamanan'],
                            ].map(([href, label]) => (
                                <a
                                    key={href}
                                    href={href}
                                    className="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-300 dark:hover:bg-white/5 dark:hover:text-white"
                                >
                                    {label}
                                </a>
                            ))}
                        </nav>

                        <div className="flex items-center gap-2">
                            {auth.user ? (
                                <Link
                                    href={route('modules.index')}
                                    className="inline-flex min-h-11 items-center justify-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                >
                                    Lanjut belajar
                                </Link>
                            ) : (
                                <>
                                    {canLogin && (
                                        <Link
                                            href={route('login')}
                                            className="inline-flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold text-slate-700 transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-slate-200 dark:hover:bg-white/5"
                                        >
                                            Masuk
                                        </Link>
                                    )}
                                    {canRegister && (
                                        <Link
                                            href={route('register')}
                                            className="inline-flex min-h-11 items-center justify-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                        >
                                            Daftar
                                        </Link>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                </header>

                <main id="konten-utama">
                    {/* Hero */}
                    <section className="relative isolate overflow-hidden">
                        <div
                            aria-hidden="true"
                            className="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(60rem_40rem_at_15%_-10%,rgba(37,99,235,0.18),transparent),radial-gradient(45rem_30rem_at_90%_0%,rgba(16,185,129,0.16),transparent)] dark:bg-[radial-gradient(60rem_40rem_at_15%_-10%,rgba(37,99,235,0.32),transparent),radial-gradient(45rem_30rem_at_90%_0%,rgba(16,185,129,0.22),transparent)]"
                        />
                        <div className="mx-auto grid max-w-6xl gap-12 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-24">
                            <div>
                                <p className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-800 dark:border-blue-400/30 dark:bg-blue-500/10 dark:text-blue-200">
                                    <span aria-hidden="true" className="h-2 w-2 rounded-full bg-emerald-500" />
                                    ISO/IEC 27001 Awareness
                                </p>

                                <h1 className="mt-5 text-balance text-4xl font-extrabold leading-[1.08] tracking-tight sm:text-5xl lg:text-6xl">
                                    Keamanan informasi dimulai dari{' '}
                                    <span className="bg-gradient-to-r from-blue-700 via-blue-600 to-emerald-600 bg-clip-text text-transparent dark:from-blue-300 dark:via-sky-300 dark:to-emerald-300">
                                        kebiasaan kecil
                                    </span>
                                    .
                                </h1>

                                <p className="mt-5 max-w-xl text-pretty text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-300">
                                    GISO27001 membantu pegawai mengenali risiko siber lewat pelajaran mikro, studi kasus nyata, dan kuis singkat. Tanpa jargon teknis, tanpa menyita waktu kerja.
                                </p>

                                <div className="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <Link
                                        href={primaryHref}
                                        className="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-700 px-6 text-base font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                    >
                                        {auth.user ? 'Lanjutkan pembelajaran' : 'Mulai belajar sekarang'}
                                        <svg aria-hidden="true" viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
                                            <path d="M5 12h14m-6-6 6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
                                        </svg>
                                    </Link>

                                    <a
                                        href="#alur"
                                        className="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 bg-white px-6 text-base font-semibold text-slate-800 transition hover:border-slate-400 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-white/15 dark:bg-white/5 dark:text-white dark:hover:bg-white/10"
                                    >
                                        Lihat cara kerjanya
                                    </a>
                                </div>

                                <dl className="mt-10 grid max-w-lg grid-cols-3 gap-4 border-t border-slate-200 pt-6 dark:border-white/10">
                                    {[
                                        ['5', 'modul dasar'],
                                        ['2–10', 'menit per sesi'],
                                        ['1', 'sertifikat awareness'],
                                    ].map(([value, label]) => (
                                        <div key={label}>
                                            <dt className="sr-only">{label}</dt>
                                            <dd className="text-2xl font-bold tracking-tight sm:text-3xl">{value}</dd>
                                            <dd className="mt-1 text-xs font-medium text-slate-500 sm:text-sm dark:text-slate-400">{label}</dd>
                                        </div>
                                    ))}
                                </dl>
                            </div>

                            {/* Pratinjau antarmuka: dekoratif, tidak mengekspos data pengguna */}
                            <div className="relative mx-auto w-full max-w-md lg:max-w-none">
                                <div
                                    aria-hidden="true"
                                    className="absolute -inset-3 -z-10 rounded-[2rem] bg-gradient-to-br from-blue-600/20 via-transparent to-emerald-500/20 blur-2xl"
                                />
                                <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/5 dark:border-white/10 dark:bg-slate-900">
                                    <div className="flex items-center justify-between gap-3">
                                        <div>
                                            <p className="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                                Progres Anda
                                            </p>
                                            <p className="mt-1 text-lg font-bold">Jalur Pembelajaran</p>
                                        </div>
                                        <span className="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">
                                            Level 2
                                        </span>
                                    </div>

                                    <div className="mt-5">
                                        <div className="flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400">
                                            <span>1 dari 5 modul selesai</span>
                                            <span>20%</span>
                                        </div>
                                        <div className="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                                            <div className="h-full w-1/5 rounded-full bg-gradient-to-r from-blue-600 to-emerald-500" />
                                        </div>
                                    </div>

                                    <ul className="mt-5 space-y-2.5">
                                        {previewModules.map((module) => (
                                            <li
                                                key={module.level}
                                                className="flex items-center gap-3 rounded-xl border border-slate-200 p-3 dark:border-white/10"
                                            >
                                                <span
                                                    aria-hidden="true"
                                                    className={
                                                        module.status === 'selesai'
                                                            ? 'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'
                                                            : module.status === 'aktif'
                                                              ? 'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'
                                                              : 'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400'
                                                    }
                                                >
                                                    <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
                                                        {module.status === 'selesai' ? (
                                                            <path d="m5 13 4 4L19 7" strokeLinecap="round" strokeLinejoin="round" />
                                                        ) : module.status === 'aktif' ? (
                                                            <path d="M8 5v14l11-7-11-7Z" strokeLinejoin="round" />
                                                        ) : (
                                                            <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v9H6v-9Z" strokeLinecap="round" strokeLinejoin="round" />
                                                        )}
                                                    </svg>
                                                </span>
                                                <div className="min-w-0 flex-1">
                                                    <p className="truncate text-sm font-semibold">
                                                        {module.level}. {module.title}
                                                    </p>
                                                    <p className="text-xs text-slate-500 dark:text-slate-400">
                                                        ISO {module.control} · {module.minutes} menit
                                                    </p>
                                                </div>
                                                <span className="shrink-0 text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                                    {module.status === 'selesai' ? 'Selesai' : module.status === 'aktif' ? 'Mulai' : 'Kunci'}
                                                </span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Manfaat */}
                    <section id="manfaat" className="border-t border-slate-200 bg-white py-16 sm:py-20 dark:border-white/10 dark:bg-slate-900/40">
                        <div className="mx-auto max-w-6xl px-4 sm:px-6">
                            <div className="max-w-2xl">
                                <p className="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700 dark:text-blue-300">Mengapa GISO27001</p>
                                <h2 className="mt-3 text-balance text-3xl font-bold tracking-tight sm:text-4xl">
                                    Dirancang untuk pegawai, bukan hanya tim IT
                                </h2>
                                <p className="mt-4 text-base leading-relaxed text-slate-600 dark:text-slate-300">
                                    Materi disusun agar mudah dipahami lintas usia dan divisi, dengan langkah yang jelas dan hasil yang bisa diukur.
                                </p>
                            </div>

                            <ul className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                {benefits.map((benefit) => (
                                    <li
                                        key={benefit.title}
                                        className="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-6 transition hover:border-blue-300 hover:bg-white dark:border-white/10 dark:bg-white/5 dark:hover:border-blue-400/40"
                                    >
                                        <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-700 text-white">
                                            <svg aria-hidden="true" viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <path d={benefit.icon} />
                                            </svg>
                                        </span>
                                        <div>
                                            <h3 className="text-base font-bold">{benefit.title}</h3>
                                            <p className="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{benefit.description}</p>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </section>

                    {/* Alur belajar */}
                    <section id="alur" className="py-16 sm:py-20">
                        <div className="mx-auto max-w-6xl px-4 sm:px-6">
                            <div className="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700 dark:text-emerald-300">Cara belajar</p>
                                    <h2 className="mt-3 text-balance text-3xl font-bold tracking-tight sm:text-4xl">Tiga langkah, satu kebiasaan</h2>
                                    <p className="mt-4 text-base leading-relaxed text-slate-600 dark:text-slate-300">
                                        Setiap modul selesai dalam sekali duduk. Lulus kuis akan membuka modul berikutnya sehingga pemahaman terbangun bertahap.
                                    </p>
                                    <p className="mt-4 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                                        Belum lulus? Tidak masalah. Anda dapat mengulang sebagai latihan. Poin penyelesaian hanya diberikan sekali agar capaian tetap bermakna.
                                    </p>
                                </div>

                                <ol className="grid gap-4">
                                    {steps.map(([title, description], index) => (
                                        <li
                                            key={title}
                                            className="flex gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-slate-900"
                                        >
                                            <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white dark:bg-white dark:text-slate-900">
                                                {index + 1}
                                            </span>
                                            <div>
                                                <h3 className="text-base font-bold">{title}</h3>
                                                <p className="mt-1 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{description}</p>
                                            </div>
                                        </li>
                                    ))}
                                </ol>
                            </div>
                        </div>
                    </section>

                    {/* Modul */}
                    <section id="modul" className="border-t border-slate-200 bg-white py-16 sm:py-20 dark:border-white/10 dark:bg-slate-900/40">
                        <div className="mx-auto max-w-6xl px-4 sm:px-6">
                            <div className="flex flex-wrap items-end justify-between gap-4">
                                <div className="max-w-2xl">
                                    <p className="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700 dark:text-blue-300">Kurikulum awal</p>
                                    <h2 className="mt-3 text-balance text-3xl font-bold tracking-tight sm:text-4xl">Lima fondasi keamanan informasi</h2>
                                </div>
                                <Link
                                    href={primaryHref}
                                    className="inline-flex min-h-11 items-center gap-2 rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-800 transition hover:bg-slate-50 dark:border-white/15 dark:text-white dark:hover:bg-white/5"
                                >
                                    Buka daftar modul
                                    <span aria-hidden="true">→</span>
                                </Link>
                            </div>

                            <ul className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                {[
                                    ['Dasar Keamanan Informasi', 'Kerahasiaan, integritas, dan ketersediaan data dalam pekerjaan sehari-hari.'],
                                    ['Waspada Phishing & Rekayasa Sosial', 'Mengenali pesan palsu dan memverifikasi permintaan mencurigakan.'],
                                    ['Password Kuat & MFA', 'Mengelola kredensial dengan aman dan memahami autentikasi multifaktor.'],
                                    ['Perlindungan Data & Privasi', 'Membagikan informasi hanya kepada pihak berwenang sesuai kebutuhan.'],
                                    ['Keamanan Perangkat & Insiden', 'Mengunci perangkat dan melaporkan insiden melalui kanal resmi.'],
                                    ['Sertifikat & Pencapaian', 'Menyelesaikan seluruh modul wajib untuk memperoleh sertifikat awareness.'],
                                ].map(([title, description], index) => (
                                    <li
                                        key={title}
                                        className="flex flex-col rounded-2xl border border-slate-200 p-6 transition hover:border-blue-300 hover:shadow-md dark:border-white/10 dark:hover:border-blue-400/40"
                                    >
                                        <span className="text-xs font-bold uppercase tracking-wider text-slate-400">
                                            {index < 5 ? `Modul ${index + 1}` : 'Penghargaan'}
                                        </span>
                                        <h3 className="mt-2 text-lg font-bold leading-snug">{title}</h3>
                                        <p className="mt-2 grow text-sm leading-relaxed text-slate-600 dark:text-slate-300">{description}</p>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </section>

                    {/* Keamanan & catatan tanggung jawab */}
                    <section id="keamanan" className="py-16 sm:py-20">
                        <div className="mx-auto max-w-6xl px-4 sm:px-6">
                            <div className="grid gap-6 rounded-2xl border border-slate-200 bg-slate-900 p-6 text-slate-100 sm:p-10 lg:grid-cols-2 dark:border-white/10">
                                <div>
                                    <p className="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-300">Keamanan sejak awal</p>
                                    <h2 className="mt-3 text-balance text-2xl font-bold tracking-tight sm:text-3xl">
                                        Akun terlindungi, data tetap terkendali
                                    </h2>
                                    <p className="mt-4 text-sm leading-relaxed text-slate-300">
                                        Sesi dilindungi dari penyalahgunaan, login dibatasi dari percobaan berulang, dan akun nonaktif tidak dapat mengakses aplikasi. Setiap pengguna hanya dapat mengubah konten miliknya sendiri.
                                    </p>
                                </div>
                                <ul className="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-1">
                                    {[
                                        'Password di-hash, bukan disimpan sebagai teks biasa.',
                                        'Akses administrator dipisahkan dengan role dan policy.',
                                        'Konten forum ditampilkan sebagai teks aman.',
                                        'Sertifikat diunduh privat dan dapat diverifikasi.',
                                    ].map((item) => (
                                        <li key={item} className="flex items-start gap-3 rounded-xl bg-white/5 p-3">
                                            <svg aria-hidden="true" viewBox="0 0 24 24" className="mt-0.5 h-4 w-4 shrink-0 text-emerald-300" fill="none" stroke="currentColor" strokeWidth="2.5">
                                                <path d="m5 13 4 4L19 7" strokeLinecap="round" strokeLinejoin="round" />
                                            </svg>
                                            <span className="text-slate-200">{item}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>

                            <p className="mx-auto mt-6 max-w-3xl text-center text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                GISO27001 adalah media pembelajaran awareness internal. Penyelesaian modul dan sertifikat di platform ini bukan merupakan sertifikasi kepatuhan ISO/IEC 27001 untuk organisasi.
                            </p>
                        </div>
                    </section>

                    {/* Ajakan akhir */}
                    <section className="border-t border-slate-200 bg-white py-16 sm:py-20 dark:border-white/10 dark:bg-slate-900/40">
                        <div className="mx-auto max-w-3xl px-4 text-center sm:px-6">
                            <h2 className="text-balance text-3xl font-bold tracking-tight sm:text-4xl">Siap memulai langkah aman pertama?</h2>
                            <p className="mx-auto mt-4 max-w-xl text-base leading-relaxed text-slate-600 dark:text-slate-300">
                                Satu modul hari ini lebih baik daripada rencana besar yang tertunda.
                            </p>
                            <div className="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                                <Link
                                    href={primaryHref}
                                    className="inline-flex min-h-12 items-center justify-center rounded-xl bg-blue-700 px-6 text-base font-semibold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                >
                                    {auth.user ? 'Lanjutkan belajar' : 'Masuk dan mulai belajar'}
                                </Link>
                                {!auth.user && canRegister && (
                                    <Link
                                        href={route('register')}
                                        className="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-300 px-6 text-base font-semibold text-slate-800 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:border-white/15 dark:text-white dark:hover:bg-white/5"
                                    >
                                        Buat akun
                                    </Link>
                                )}
                            </div>
                        </div>
                    </section>
                </main>

                <footer className="border-t border-slate-200 py-8 dark:border-white/10">
                    <div className="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6">
                        <div className="flex items-center gap-3">
                            <BrandLogo className="h-8 w-8" />
                            <p className="text-sm font-semibold">GISO27001</p>
                        </div>
                        <p className="text-xs text-slate-500 dark:text-slate-400">
                            Platform pembelajaran kesadaran keamanan informasi ISO/IEC 27001.
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}
