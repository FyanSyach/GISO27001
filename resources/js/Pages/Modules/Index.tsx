import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

type Module = { id: number; slug: string; title: string; description: string; order_level: number; points_reward: number; completed: boolean; locked: boolean };

/** Katalog modul berjenjang dengan status akses dan penyelesaian dari server. */
export default function Index({ modules }: { modules: Module[] }) {
    return <AuthenticatedLayout><Head title="Modul Belajar" /><main className="mx-auto max-w-6xl px-4 py-10 dark:text-gray-100">
        <Link href={route('achievements.index')} className="inline-flex min-h-12 items-center text-blue-700 dark:text-blue-300">Pencapaian, challenge & sertifikat →</Link>
        <h1 className="text-3xl font-bold">Belajar melindungi informasi</h1>
        <p className="mt-3 text-gray-600 dark:text-gray-300">Pelajari satu langkah kecil setiap hari. Lulus kuis untuk membuka modul berikutnya.</p>
        <div className="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">{modules.map(module => <article key={module.id} className="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
            <span className="text-sm font-semibold">Modul {module.order_level} · {module.completed ? 'Selesai' : module.locked ? 'Terkunci' : 'Tersedia'}</span>
            <h2 className="text-xl font-bold">{module.title}</h2><p className="grow text-gray-600 dark:text-gray-300">{module.description}</p>
            {module.locked ? <p>Selesaikan modul sebelumnya.</p> : <Link className="inline-flex min-h-12 items-center justify-center rounded-lg bg-blue-700 px-4 text-white focus-visible:outline-2" href={route('modules.show', module.slug)}>{module.completed ? 'Latihan ulang' : 'Mulai belajar'}</Link>}
        </article>)}</div>
        {modules.length === 0 && <p className="mt-8 rounded-lg bg-white p-6 dark:bg-gray-800">Modul sedang disiapkan. Silakan kembali lagi nanti.</p>}
    </main></AuthenticatedLayout>;
}
