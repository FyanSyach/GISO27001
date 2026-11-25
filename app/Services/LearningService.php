<?php

namespace App\Services;

use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Mengelola urutan akses, penyelesaian, dan penilaian kuis secara atomik. */
class LearningService
{
    /** Menolak modul draft atau prasyarat belum selesai untuk pengguna yang diberikan. */
    public function ensureAccessible(User $user, Module $module): void
    {
        abort_unless($module->is_published, 404);
        $blocked = Module::where('is_published', true)->where('order_level', '<', $module->order_level)
            ->whereDoesntHave('progress', fn ($query) => $query->where('user_id', $user->id)->where('is_completed', true))->exists();
        abort_if($blocked, 403, 'Selesaikan modul sebelumnya terlebih dahulu.');
    }

    /** Menilai jawaban milik modul; reward hanya pada kelulusan pertama dan tidak dipercaya dari klien.
     * @param  array<string, string>  $answers  Jawaban berdasarkan ID pertanyaan.
     * @return array<string, mixed> Ringkasan nilai dan pembahasan untuk sesi pengguna.
     */
    public function submit(User $user, Module $module, array $answers): array
    {
        return DB::transaction(function () use ($user, $module, $answers): array {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->ensureAccessible($locked, $module);
            $questions = $module->quizzes()->orderBy('id')->get();
            if ($questions->isEmpty() || $module->contents()->doesntExist()) {
                throw ValidationException::withMessages(['answers' => 'Materi atau kuis modul belum tersedia.']);
            }
            $expected = $questions->pluck('id')->map(fn ($id) => (string) $id)->sort()->values()->all();
            $provided = array_map('strval', array_keys($answers));
            sort($provided, SORT_STRING);
            sort($expected, SORT_STRING);
            if ($provided !== $expected) {
                throw ValidationException::withMessages(['answers' => 'Jawab seluruh pertanyaan modul ini tanpa pertanyaan tambahan.']);
            }
            foreach ($questions as $question) {
                if (! array_key_exists($answers[$question->id], $question->options)) {
                    throw ValidationException::withMessages(['answers' => 'Pilihan jawaban tidak valid.']);
                }
            }
            $progress = UserProgress::firstOrCreate(['user_id' => $locked->id, 'module_id' => $module->id]);
            $correct = $questions->filter(fn ($q) => $answers[$q->id] === $q->correct_answer)->count();
            $score = (int) floor($correct / $questions->count() * 100);
            $passed = $score >= $module->passing_score;
            // points_awarded menjadi penanda permanen kelulusan pertama, termasuk jika reward bernilai nol.
            $firstPass = $passed && ! $progress->points_awarded;
            $quizReward = $firstPass ? max(10, (int) round($score / 100 * 50)) : 0;
            $moduleReward = $firstPass ? $module->points_reward : 0;
            $number = (int) $locked->hasMany(QuizAttempt::class)->whereIn('quiz_id', $questions->pluck('id'))->max('attempt_number') + 1;
            $feedback = [];
            foreach ($questions as $index => $question) {
                $isCorrect = $answers[$question->id] === $question->correct_answer;
                $question->attempts()->create(['user_id' => $locked->id, 'selected_answer' => $answers[$question->id],
                    'is_correct' => $isCorrect, 'attempt_number' => $number, 'points_earned' => $index === 0 ? $quizReward : 0]);
                $feedback[] = ['id' => $question->id, 'question' => $question->question, 'correct' => $isCorrect,
                    'correct_answer' => $question->correct_answer, 'explanation' => $question->explanation];
            }
            if ($firstPass) {
                $progress->update(['is_completed' => true, 'points_awarded' => true, 'completed_at' => now()]);
                app(GamificationService::class)->grantPoints($locked, $moduleReward + $quizReward);
                app(ChallengeService::class)->moduleCompleted($locked);
            }
            // Kuis sah yang lulus, termasuk latihan hari berikutnya, dihitung sebagai sesi belajar.
            if ($passed) {
                app(StreakTrackerService::class)->track($locked);
            }

            return ['module_id' => $module->id, 'score' => $score, 'passed' => $passed, 'attempt' => $number,
                'points' => $moduleReward + $quizReward, 'practice' => ! $firstPass && (bool) $progress->points_awarded, 'feedback' => $feedback];
        }, 3);
    }
}
