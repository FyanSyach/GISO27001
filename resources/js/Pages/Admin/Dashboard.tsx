import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Link } from '@inertiajs/react';
/** Ringkasan analitik dan navigasi pengelolaan khusus admin. */
export default function Dashboard({ stats }: { stats: Record<string, number> }) {
    return <AuthenticatedLayout><main className="mx-auto max-w-6xl p-6"><h1 className="text-3xl font-bold">Dashboard Admin</h1><div className="my-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">{Object.entries(stats).map(([name, count]) => <article className="rounded-lg bg-white p-6" key={name}><h2>{name}</h2><strong className="text-3xl">{count}</strong></article>)}</div><nav className="flex flex-wrap gap-5"><Link href={route('admin.users.index')}>Kelola pengguna</Link><Link href={route('admin.modules.index')}>Kelola konten</Link><a href={route('admin.reports.excel')}>Ekspor Excel</a><a href={route('admin.reports.pdf')}>Ekspor PDF (maks. 500 akun)</a></nav></main></AuthenticatedLayout>;
}
