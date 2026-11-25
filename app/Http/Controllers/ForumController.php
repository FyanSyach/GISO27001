<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/** Mengelola diskusi dan balasan dengan otorisasi kepemilikan pada server. */
class ForumController extends Controller
{
    /** Menampilkan diskusi terbaru secara terpaginasikan. */
    public function index(): Response
    {
        return Inertia::render('Forum/Index', ['threads' => ForumThread::with('user:id,name')->withCount('replies')->latest()->paginate(20)]);
    }

    /** Menampilkan thread dan balasan tanpa memuat seluruh forum. */
    public function show(ForumThread $thread): Response
    {
        return Inertia::render('Forum/Show', ['thread' => $thread->load('user:id,name'), 'replies' => $thread->replies()->with('user:id,name')->orderBy('id')->paginate(30)]);
    }

    /** Membuat diskusi dari input tervalidasi; identitas pemilik berasal dari sesi. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['title' => 'required|string|max:200', 'category' => 'required|string|max:100', 'content' => 'required|string|max:10000']);
        $thread = ForumThread::create([...$data, 'user_id' => $request->user()->id]);

        return to_route('forum.show', $thread);
    }

    /** Menyunting diskusi hanya setelah policy menyetujui. */
    public function update(Request $request, ForumThread $thread): RedirectResponse
    {
        Gate::authorize('update', $thread);
        $thread->update($request->validate(['title' => 'required|string|max:200', 'category' => 'required|string|max:100', 'content' => 'required|string|max:10000']));

        return back();
    }

    /** Menghapus thread yang diizinkan policy beserta balasannya melalui cascade. */
    public function destroy(ForumThread $thread): RedirectResponse
    {
        Gate::authorize('delete', $thread);
        $thread->delete();

        return to_route('forum.index');
    }

    /** Menambahkan balasan ke thread yang tidak dikunci. */
    public function reply(Request $request, ForumThread $thread): RedirectResponse
    {
        abort_if($thread->is_locked, 403);
        $data = $request->validate(['content' => 'required|string|max:10000']);
        $thread->replies()->create([...$data, 'user_id' => $request->user()->id]);

        return back();
    }

    /** Menyunting balasan hanya milik sendiri pada thread terbuka. */
    public function updateReply(Request $request, ForumReply $reply): RedirectResponse
    {
        abort_unless($reply->user_id === $request->user()->id && ! $reply->thread->is_locked, 403);
        $reply->update($request->validate(['content' => 'required|string|max:10000']));

        return back();
    }

    /** Menghapus balasan sendiri atau moderasi admin dan membersihkan solusi terkait. */
    public function deleteReply(Request $request, ForumReply $reply): RedirectResponse
    {
        abort_unless($reply->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        DB::transaction(function () use ($reply): void {
            $reply->thread()->where('best_reply_id', $reply->id)->update(['best_reply_id' => null]);
            $reply->delete();
        });

        return back();
    }

    /** Menandai solusi hanya dari balasan thread yang sama dan oleh pemilik. */
    public function solution(Request $request, ForumThread $thread): RedirectResponse
    {
        Gate::authorize('update', $thread);
        $data = $request->validate(['reply_id' => 'required|integer']);
        $reply = $thread->replies()->findOrFail($data['reply_id']);
        $thread->update(['best_reply_id' => $reply->id]);

        return back();
    }
}
