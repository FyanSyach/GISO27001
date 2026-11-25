<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserProgress;
use App\Services\CertificateService;
use App\Services\ChallengeService;
use App\Services\GamificationService;
use App\Services\StreakTrackerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** Memverifikasi insentif, hari kalender dan kelayakan sertifikat. */
class GamificationTest extends TestCase
{
    use RefreshDatabase;

    /** Poin memperbarui level terbatas dan badge tidak diduplikasi. */
    public function test_points_award_badge_once_and_cap_level(): void
    {
        $user = User::factory()->create();
        Badge::factory()->create(['min_points' => 100]);
        app(GamificationService::class)->grantPoints($user, 1200);
        app(GamificationService::class)->grantPoints($user, -10);
        $this->assertSame(1200, $user->fresh()->points);
        $this->assertSame(10, $user->fresh()->level);
        $this->assertDatabaseCount('user_badges', 1);
    }

    /** Aktivitas hari yang sama tidak menambah reward; jeda hari mereset streak. */
    public function test_streak_uses_calendar_days(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 25)->startOfDay());
        $user = User::factory()->create();
        app(StreakTrackerService::class)->track($user);
        app(StreakTrackerService::class)->track($user);
        $this->assertSame(10, $user->fresh()->points);
        $this->travel(1)->days();
        app(StreakTrackerService::class)->track($user);
        $this->assertSame(2, $user->fresh()->current_streak);
        $this->travel(2)->days();
        app(StreakTrackerService::class)->track($user);
        $this->assertSame(1, $user->fresh()->current_streak);
        $this->travelBack();
    }

    /** Challenge aktif membayar sekali; tantangan kedaluwarsa tidak menerima progres. */
    public function test_challenge_claims_reward_once(): void
    {
        $user = User::factory()->create();
        Challenge::factory()->create(['points_reward' => 15]);
        Challenge::factory()->create(['ends_at' => now()->subDay(), 'starts_at' => now()->subDays(2)]);
        app(ChallengeService::class)->moduleCompleted($user);
        app(ChallengeService::class)->moduleCompleted($user);
        $this->assertSame(15, $user->fresh()->points);
        $this->assertDatabaseCount('user_challenges', 1);
    }

    /** Ranking individu dan agregasi divisi tidak memasukkan akun nonaktif. */
    public function test_leaderboard_excludes_inactive_accounts(): void
    {
        $active = User::factory()->create(['division' => 'HR', 'points' => 50]);
        User::factory()->create(['division' => 'HR', 'points' => 9000, 'is_active' => false]);
        $this->actingAs($active)->get(route('achievements.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Gamification/Index')->has('leaders', 1)
                ->where('leaders.0.id', $active->id)
                ->where('divisions.0.total_points', 50));
    }

    /** Sertifikat hanya terbit setelah progres dan bukti kelulusan, serta PDF bisa diunduh. */
    public function test_certificate_requires_pass_and_generates_private_pdf(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $module = Module::factory()->create(['is_published' => true]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id]);
        $this->actingAs($user)->get(route('certificate.download'))->assertSessionHasErrors('certificate');
        UserProgress::factory()->create(['user_id' => $user->id, 'module_id' => $module->id, 'is_completed' => true, 'points_awarded' => true]);
        QuizAttempt::factory()->create(['user_id' => $user->id, 'quiz_id' => $quiz->id]);
        $certificate = app(CertificateService::class)->issue($user);
        $this->assertStringStartsWith('%PDF-', Storage::disk('local')->get($certificate->file_path));
        app(CertificateService::class)->issue($user);
        $this->assertDatabaseCount('certificates', 1);
    }
}
