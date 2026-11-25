<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\UserProgress;
use App\Services\LearningService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Menyajikan katalog dan materi pembelajaran tanpa membocorkan kunci kuis. */
class LearningModuleController extends Controller
{
    /** Menampilkan modul terbit berurutan dan status kunci berdasarkan progres pengguna. */
    public function index(Request $request): Response
    {
        $completed = UserProgress::where('user_id', $request->user()->id)->where('is_completed', true)->pluck('module_id');
        $unlocked = true;
        $modules = Module::where('is_published', true)->orderBy('order_level')->get()->map(function ($module) use ($completed, &$unlocked): array {
            $done = $completed->contains($module->id);
            $item = [...$module->only(['id', 'slug', 'title', 'description', 'order_level', 'passing_score', 'points_reward']), 'completed' => $done, 'locked' => ! $unlocked];
            if (! $done) {
                $unlocked = false;
            }

            return $item;
        });

        return Inertia::render('Modules/Index', ['modules' => $modules]);
    }

    /** Menampilkan konten terurut dan pertanyaan aman; hasil terakhir dibatasi pada modul terkait. */
    public function show(Request $request, Module $module, LearningService $learning): Response
    {
        $learning->ensureAccessible($request->user(), $module);
        $module->load(['contents' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')]);
        $questions = $module->quizzes()->orderBy('id')->get(['id', 'module_id', 'question', 'options']);
        $result = $request->session()->get('quiz_result');

        return Inertia::render('Modules/Show', ['module' => $module->only(['id', 'slug', 'title', 'description', 'passing_score']),
            'contents' => $module->contents, 'questions' => $questions,
            'result' => ($result['module_id'] ?? null) === $module->id ? $result : null]);
    }
}
