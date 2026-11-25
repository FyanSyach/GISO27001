<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Menerbitkan PDF awareness privat; bukan sertifikasi kepatuhan organisasi ISO. */
class CertificateService
{
    /** Memvalidasi seluruh modul terbit sebagai modul wajib, lalu membuat satu sertifikat per pengguna. */
    public function issue(User $user): Certificate
    {
        return DB::transaction(function () use ($user): Certificate {
            $locked = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $modules = Module::where('is_published', true)->where('is_required', true)->with('quizzes')->get();
            if ($modules->isEmpty()) {
                throw ValidationException::withMessages(['certificate' => 'Belum ada modul wajib yang diterbitkan.']);
            }
            foreach ($modules as $module) {
                $progress = $locked->progress()->where('module_id', $module->id)->where('is_completed', true)->where('points_awarded', true)->exists();
                $attempts = QuizAttempt::where('user_id', $locked->id)->whereIn('quiz_id', $module->quizzes->pluck('id'))->get()->groupBy('attempt_number');
                $passed = $attempts->contains(fn ($items) => $items->count() === $module->quizzes->count() && $items->count() > 0 && floor($items->where('is_correct', true)->count() / $items->count() * 100) >= $module->passing_score);
                if (! $progress || ! $passed) {
                    throw ValidationException::withMessages(['certificate' => 'Selesaikan dan lulus seluruh modul wajib terlebih dahulu.']);
                }
            }
            $certificate = Certificate::where('user_id', $locked->id)->first();
            if (! $certificate) {
                $number = 'GISO-'.Str::uuid();
                $certificate = new Certificate(['user_id' => $locked->id, 'certificate_number' => $number, 'file_path' => 'certificates/'.$number.'.pdf', 'issued_at' => now()]);
            }
            if (! Storage::disk('local')->exists($certificate->file_path)) {
                $bytes = Pdf::loadView('certificates.awareness', ['user' => $locked, 'certificate' => $certificate])->setPaper('a4', 'landscape')->output();
                if (! Storage::disk('local')->put($certificate->file_path, $bytes)) {
                    throw new \RuntimeException('Gagal menyimpan PDF sertifikat.');
                }
            }
            $certificate->save();

            return $certificate;
        }, 3);
    }
}
