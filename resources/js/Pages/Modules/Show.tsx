import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

type Content = { id: number; title: string; content_type: string; body: string | null; media_url: string | null; subtitle_url: string | null; duration_minutes: number };
type Question = { id: number; question: string; options: Record<string, string> };
type Result = { score: number; passed: boolean; points: number; practice: boolean; feedback: { id: number; question: string; correct: boolean; correct_answer: string; explanation: string | null }[] };

/** Mengizinkan hanya URL media HTTP(S) atau path lokal tanpa HTML aktif. */
function safeMedia(value: string | null): string | undefined {
    if (!value) return undefined;
    if (value.startsWith('/') && !value.startsWith('//') && !value.includes('\\')) return value;
    try { const url = new URL(value); return ['http:', 'https:'].includes(url.protocol) ? url.href : undefined; } catch { return undefined; }
}

/** Menampilkan materi aman, pemutar video dengan subtitle, kuis, serta hasil penilaian. */
export default function Show({ module, contents, questions, result }: { module: { slug: string; title: string; description: string; passing_score: number }; contents: Content[]; questions: Question[]; result: Result | null }) {
    const form = useForm<{ answers: Record<string, string> }>({ answers: {} });
    return <AuthenticatedLayout><Head title={module.title} /><main className="mx-auto flex max-w-4xl flex-col gap-6 px-4 py-10 dark:text-gray-100">
        <Link href={route('modules.index')} className="inline-flex min-h-12 items-center text-blue-700 dark:text-blue-300">← Semua modul</Link>
        <h1 className="text-3xl font-bold">{module.title}</h1><p>{module.description}</p>
        {contents.map(content => <section key={content.id} className="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
            <h2 className="text-xl font-bold">{content.title}</h2><p className="text-sm">{content.duration_minutes} menit · {content.content_type}</p>
            {content.body && <p className="whitespace-pre-wrap break-words">{content.body}</p>}
            {content.content_type === 'video' && safeMedia(content.media_url) && <video className="w-full rounded-lg" controls preload="metadata" crossOrigin="anonymous"><source src={safeMedia(content.media_url)} />{safeMedia(content.subtitle_url) && <track kind="subtitles" src={safeMedia(content.subtitle_url)} srcLang="id" label="Bahasa Indonesia" default />}Browser Anda tidak mendukung video.</video>}
            {content.content_type === 'infographic' && safeMedia(content.media_url) && <img className="h-auto max-w-full rounded-lg" src={safeMedia(content.media_url)} alt={content.title} loading="lazy" />}
        </section>)}
        {contents.length === 0 && <p>Materi belum tersedia.</p>}
        <form className="flex flex-col gap-6 rounded-xl bg-white p-6 dark:bg-gray-800" onSubmit={event => { event.preventDefault(); form.post(route('modules.quiz.store', module.slug), { preserveScroll: true }); }}>
            <h2 className="text-2xl font-bold">Uji pemahaman</h2><p>Nilai kelulusan: {module.passing_score}%. Pengulangan setelah lulus tidak menambah poin.</p>
            {questions.map((question, index) => <fieldset key={question.id}><legend className="font-semibold">{index + 1}. {question.question}</legend><div className="mt-3 flex flex-col gap-2">{Object.entries(question.options).map(([key, label]) => <label key={key} className="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border border-gray-300 p-3"><input required type="radio" name={`question-${question.id}`} value={key} checked={form.data.answers[question.id] === key} onChange={() => form.setData('answers', { ...form.data.answers, [question.id]: key })} /><span>{label}</span></label>)}</div></fieldset>)}
            {Object.values(form.errors).map((error, index) => <p key={index} role="alert" className="text-red-600">{error}</p>)}
            <button disabled={form.processing || !questions.length || !contents.length} className="min-h-12 rounded-lg bg-blue-700 px-5 text-white disabled:opacity-50">{form.processing ? 'Menilai...' : 'Kirim jawaban'}</button>
        </form>
        {result && <section aria-live="polite" className="flex flex-col gap-4 rounded-xl bg-white p-6 dark:bg-gray-800"><h2 className="text-2xl font-bold">{result.passed ? 'Selamat, Anda lulus!' : 'Belum lulus, coba kembali'} · {result.score}%</h2><p>Tambahan poin: {result.points}{result.practice ? ' · Mode latihan' : ''}</p>{result.feedback.map(item => <div key={item.id}><h3 className="font-semibold">{item.question}</h3><p>{item.correct ? 'Benar' : 'Belum tepat'} · Jawaban: {item.correct_answer}</p><p>{item.explanation}</p></div>)}</section>}
    </main></AuthenticatedLayout>;
}
