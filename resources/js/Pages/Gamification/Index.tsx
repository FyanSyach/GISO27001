import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';

type Leader = { id: number; name: string; division: string | null; points: number; level: number };
type Division = { division: string; total_points: number; average_points: number; members: number };
type Challenge = { id: number; title: string; description: string; frequency: string; target_count: number; points_reward: number };

/** Portal ranking akun aktif, progres challenge dan permintaan sertifikat privat. */
export default function Index({ leaders, divisions, challenges, progress }: { leaders: Leader[]; divisions: Division[]; challenges: Challenge[]; progress: Record<string, { progress_count: number; is_completed: boolean }> }) {
    const { errors } = usePage().props;
    const refresh = () => window.location.reload();
    return <AuthenticatedLayout><Head title="Pencapaian" /><main className="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 dark:text-gray-100"><div className="flex flex-wrap items-center justify-between gap-3"><h1 className="text-3xl font-bold">Pencapaian & Tantangan</h1><button type="button" onClick={refresh} className="min-h-12 rounded-lg border border-blue-700 px-4 text-blue-700 dark:text-blue-300">Perbarui ranking</button></div>
        <Link href={route('modules.index')} className="flex min-h-12 items-center text-blue-700 dark:text-blue-300">Kembali belajar →</Link>
        <section className="grid gap-4 sm:grid-cols-2">{challenges.map(challenge => <article key={challenge.id} className="rounded-xl bg-white p-6 dark:bg-gray-800"><h2 className="text-xl font-bold">{challenge.title}</h2><p>{challenge.description}</p><p>{challenge.frequency} · {challenge.points_reward} poin</p><p>{progress[challenge.id]?.progress_count ?? 0} / {challenge.target_count} {progress[challenge.id]?.is_completed && '· Reward diklaim'}</p></article>)}</section>
        <div className="grid gap-6 lg:grid-cols-2"><section className="rounded-xl bg-white p-6 dark:bg-gray-800"><h2 className="mb-4 text-2xl font-bold">Leaderboard individu</h2><ol className="flex flex-col gap-3">{leaders.map((user, index) => <li key={user.id} className="flex flex-wrap justify-between gap-2 border-b border-gray-200 py-3"><span>{index + 1}. {user.name} · {user.division}</span><span>{user.points} poin · Level {user.level}</span></li>)}</ol></section>
        <section className="rounded-xl bg-white p-6 dark:bg-gray-800"><h2 className="mb-4 text-2xl font-bold">Leaderboard divisi</h2>{divisions.map(division => <p key={division.division} className="border-b border-gray-200 py-3">{division.division}: {division.total_points} poin · Rata-rata {Number(division.average_points).toFixed(1)} · {division.members} anggota</p>)}</section></div>
        <section className="rounded-xl bg-white p-6 dark:bg-gray-800"><h2 className="text-2xl font-bold">Sertifikat awareness</h2><p className="my-3">Tersedia setelah seluruh modul wajib selesai dan kuisnya lulus.</p><a href={route('certificate.download')} className="inline-flex min-h-12 items-center rounded-lg bg-blue-700 px-4 text-white">Unduh PDF sertifikat</a>{errors.certificate && <p role="alert" className="mt-3 text-red-600">{errors.certificate}</p>}</section>
    </main></AuthenticatedLayout>;
}
