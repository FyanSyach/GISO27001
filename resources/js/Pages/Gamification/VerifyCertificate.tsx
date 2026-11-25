import { Head, Link } from '@inertiajs/react';

type Certificate = { number: string; name: string; division: string | null; issued_at: string } | null;

/** Halaman publik untuk memeriksa keaslian nomor sertifikat internal. */
export default function VerifyCertificate({ certificate }: { certificate: Certificate }) {
    return <><Head title="Verifikasi Sertifikat" /><main className="mx-auto max-w-xl px-4 py-16 dark:bg-gray-900 dark:text-gray-100"><section className="rounded-xl bg-white p-8 shadow-sm dark:bg-gray-800"><h1 className="text-3xl font-bold">Verifikasi Sertifikat</h1>{certificate ? <div className="mt-6 flex flex-col gap-3"><p className="text-green-700 dark:text-green-300">Nomor sertifikat valid.</p><p><strong>Nama:</strong> {certificate.name}</p><p><strong>Divisi:</strong> {certificate.division || '—'}</p><p><strong>Diterbitkan:</strong> {certificate.issued_at}</p><p className="break-all text-sm"><strong>Nomor:</strong> {certificate.number}</p></div> : <p className="mt-6 text-red-700 dark:text-red-300">Nomor sertifikat tidak ditemukan.</p>}<Link href="/" className="mt-8 inline-flex min-h-12 items-center text-blue-700 dark:text-blue-300">Kembali ke beranda</Link></section></main></>;
}
