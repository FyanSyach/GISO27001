<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\ModuleContent;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/** Pengujian batas akses dan anti-farming alur belajar. */
class LearningTest extends TestCase
{
    use RefreshDatabase;

    /** Kelulusan pertama membuka modul berikutnya tanpa menggandakan poin pada latihan. */
    public function test_first_pass_awards_points_once_and_unlocks_next_module(): void
    {
        $user = User::factory()->create();
        $module = Module::factory()->create(['order_level' => 1, 'is_published' => true]);
        ModuleContent::factory()->create(['module_id' => $module->id]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id]);
        $next = Module::factory()->create(['order_level' => 2, 'is_published' => true]);
        $this->actingAs($user)->get(route('modules.show', $next))->assertForbidden();
        $this->post(route('modules.quiz.store', $module), ['answers' => [$quiz->id => 'B']])->assertSessionHasNoErrors();
        $this->assertSame(0, $user->fresh()->points);
        $this->post(route('modules.quiz.store', $module), ['answers' => [$quiz->id => 'A']])->assertSessionHasNoErrors();
        $this->assertSame(110, $user->fresh()->points);
        $this->post(route('modules.quiz.store', $module), ['answers' => [$quiz->id => 'A']])->assertSessionHasNoErrors();
        $this->assertSame(110, $user->fresh()->points);
        $this->assertDatabaseHas('user_progress', ['user_id' => $user->id, 'module_id' => $module->id, 'is_completed' => true, 'points_awarded' => true]);
        $this->get(route('modules.show', $next))->assertOk();
    }

    /** Pertanyaan asing dan pilihan palsu tidak menghasilkan percobaan atau poin. */
    public function test_rejects_foreign_questions_and_invalid_options(): void
    {
        $user = User::factory()->create();
        $module = Module::factory()->create(['is_published' => true]);
        ModuleContent::factory()->create(['module_id' => $module->id]);
        $quiz = Quiz::factory()->create(['module_id' => $module->id]);
        $this->actingAs($user)->post(route('modules.quiz.store', $module), ['answers' => [999 => 'A']])->assertSessionHasErrors('answers');
        $this->post(route('modules.quiz.store', $module), ['answers' => [$quiz->id => 'FAKE']])->assertSessionHasErrors('answers');
        $this->assertDatabaseCount('quiz_attempts', 0);
    }

    /** Kunci jawaban dan pembahasan tidak disertakan dalam payload awal halaman kuis. */
    public function test_question_payload_does_not_expose_answers(): void
    {
        $module = Module::factory()->create(['is_published' => true]);
        Quiz::factory()->create(['module_id' => $module->id]);
        $this->actingAs(User::factory()->create())->get(route('modules.show', $module))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Modules/Show')->has('questions', 1)
                ->missing('questions.0.correct_answer')->missing('questions.0.explanation'));
    }

    /** Tamu dialihkan ke login; modul draft tidak dapat diakses meskipun slug diketahui. */
    public function test_guests_and_draft_access_are_rejected(): void
    {
        $this->get(route('modules.index'))->assertRedirect(route('login'));
        $module = Module::factory()->create();
        $this->actingAs(User::factory()->create())->get(route('modules.show', $module))->assertNotFound();
    }
}
