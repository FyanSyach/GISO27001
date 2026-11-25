<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuizRequest;
use App\Models\Module;
use App\Services\LearningService;
use Illuminate\Http\RedirectResponse;

/** Menerima pengiriman kuis dan mengarahkan ke hasil penilaian server. */
class QuizController extends Controller
{
    /** Menilai jawaban tervalidasi dan menyimpan ringkasan hasil hanya pada sesi pengirim. */
    public function store(SubmitQuizRequest $request, Module $module, LearningService $learning): RedirectResponse
    {
        $result = $learning->submit($request->user(), $module, $request->validated('answers'));

        return to_route('modules.show', $module)->with('quiz_result', $result);
    }
}
