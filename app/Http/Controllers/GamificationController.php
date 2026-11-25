<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Challenge;
use App\Models\User;
use App\Models\UserChallenge;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Menyajikan leaderboard, challenge dan unduhan sertifikat milik pengguna login. */
class GamificationController extends Controller
{
    /** Menampilkan ranking aktif; skor divisi dihitung dari pengguna aktif saja. */
    public function index(Request $request): Response
    {
        return Inertia::render('Gamification/Index', [
            'leaders' => User::where('is_active', true)->orderByDesc('points')->orderBy('id')->limit(50)->get(['id', 'name', 'division', 'points', 'level']),
            'divisions' => User::where('is_active', true)->whereNotNull('division')->selectRaw('division, SUM(points) as total_points, AVG(points) as average_points, COUNT(*) as members')->groupBy('division')->orderByDesc('total_points')->orderBy('division')->get(),
            'challenges' => Challenge::where('starts_at', '<=', now())->where('ends_at', '>', now())->get(),
            'progress' => UserChallenge::where('user_id', $request->user()->id)->get()->keyBy('challenge_id'),
        ]);
    }

    /** Menghasilkan dan mengunduh PDF privat hanya untuk pemilik sesi. */
    /** Memverifikasi nomor sertifikat tanpa membuka data profil sensitif. */
    public function verify(string $certificateNumber): Response
    {
        $certificate = Certificate::with('user:id,name,division')->where('certificate_number', $certificateNumber)->first();

        return Inertia::render('Gamification/VerifyCertificate', ['certificate' => $certificate ? [
            'number' => $certificate->certificate_number, 'name' => $certificate->user->name,
            'division' => $certificate->user->division, 'issued_at' => $certificate->issued_at->toDateString(),
        ] : null]);
    }

    /** Menghasilkan dan mengunduh PDF privat hanya untuk pemilik sesi. */
    public function certificate(Request $request, CertificateService $service): StreamedResponse
    {
        $certificate = $service->issue($request->user());

        return Storage::disk('local')->download($certificate->file_path, $certificate->certificate_number.'.pdf', ['Content-Type' => 'application/pdf']);
    }
}
