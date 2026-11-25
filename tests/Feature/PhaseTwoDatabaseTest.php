<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Module;
use App\Models\User;
use App\Models\UserProgress;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Memverifikasi data awal, factory, dan constraint domain Fase 2. */
class PhaseTwoDatabaseTest extends TestCase
{
    use RefreshDatabase;

    /** Seed berulang tidak menggandakan data atau mengganti password operator. */
    public function test_seed_is_repeatable_and_preserves_credentials(): void
    {
        config(['seeding.admin_password' => 'AdminTestPassword!123', 'seeding.demo_password' => 'DemoTestPassword!123']);
        $this->seed(DatabaseSeeder::class);
        $hash = User::where('email', config('seeding.admin_email'))->sole()->password;
        config(['seeding.admin_password' => 'DifferentPassword!123']);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 11);
        $this->assertDatabaseCount('modules', 5);
        $this->assertDatabaseCount('badges', 3);
        $admin = User::where('email', config('seeding.admin_email'))->sole();
        $this->assertTrue($admin->isAdmin());
        $this->assertSame($hash, $admin->password);
        $this->assertTrue(Hash::check('AdminTestPassword!123', $hash));
        $this->assertSame(5, User::where('role', 'user')->distinct()->count('division'));
        $this->assertSame([1, 2, 3, 4, 5], Module::orderBy('order_level')->pluck('order_level')->all());
        $this->assertSame(0, Module::where('is_published', true)->count());
        $this->assertNotNull(Badge::where('code', 'fast-learner')->sole()->requiredModule);
    }

    /** Seluruh factory domain menghasilkan record dengan relasi valid. */
    public function test_domain_factories_persist_valid_records(): void
    {
        foreach (['Module', 'ModuleContent', 'UserProgress', 'Quiz', 'QuizAttempt', 'Badge', 'UserBadge', 'Challenge', 'UserChallenge', 'Certificate', 'ForumThread', 'ForumReply'] as $name) {
            $class = 'App\\Models\\'.$name;
            $this->assertTrue($class::factory()->create()->exists);
        }
    }

    /** Progres ganda pengguna pada modul yang sama ditolak database. */
    public function test_progress_cannot_be_duplicated(): void
    {
        $progress = UserProgress::factory()->create();
        $this->expectException(QueryException::class);
        UserProgress::factory()->create(['user_id' => $progress->user_id, 'module_id' => $progress->module_id]);
    }

    /** Seed tanpa password eksplisit tidak meninggalkan akun parsial. */
    public function test_seed_requires_explicit_credentials(): void
    {
        config(['seeding.admin_password' => null]);
        try {
            $this->seed(DatabaseSeeder::class);
            $this->fail('Seed seharusnya menolak password kosong.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('SEED_ADMIN_PASSWORD', $exception->getMessage());
        }
        $this->assertDatabaseCount('users', 0);
    }
}
