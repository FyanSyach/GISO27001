<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/** CRUD modul dan isi mikro; publikasi memerlukan materi dan minimal tiga soal. */
class ModuleManagementController extends Controller
{
    /** Mengirim modul terpaginasikan beserta isi dan soal untuk penyunting admin. */
    public function index(): Response
    {
        return Inertia::render('Admin/Modules', ['modules' => Module::with(['contents', 'quizzes'])->orderBy('order_level')->paginate(10)]);
    }

    /** Membuat modul draft; publikasi dilakukan setelah isi tersedia. */
    public function store(Request $request): RedirectResponse
    {
        Module::create($this->data($request));

        return back();
    }

    /** Menyunting metadata dan menolak publikasi modul yang belum lengkap. */
    public function update(Request $request, Module $module): RedirectResponse
    {
        $data = $this->data($request, $module);
        if ($data['is_published']) {
            abort_unless($module->contents()->exists() && $module->quizzes()->count() >= 3, 422, 'Publikasi memerlukan materi dan minimal tiga soal.');
        }
        $module->update($data);

        return back();
    }

    /** Menghapus modul hanya jika belum memiliki progres atau badge terkait. */
    public function destroy(Module $module): RedirectResponse
    {
        abort_if($module->progress()->exists() || Badge::where('required_module_id', $module->id)->exists(), 422, 'Modul sudah digunakan. Arsipkan dengan menonaktifkan publikasi.');
        $module->delete();

        return back();
    }

    /** Menambah atau menyunting konten mikro dari payload tervalidasi, tanpa HTML aktif. */
    public function content(Request $request, Module $module): RedirectResponse
    {
        abort_if($module->is_published, 422, 'Jadikan modul draft sebelum mengubah materi.');
        $data = $request->validate(['id' => 'nullable|integer', 'title' => 'required|string|max:255',
            'content_type' => ['required', Rule::in(['text', 'case_study', 'video', 'infographic'])],
            'body' => 'nullable|string|max:50000', 'media_url' => 'nullable|url:http,https|max:2048',
            'subtitle_url' => 'nullable|url:http,https|max:2048', 'duration_minutes' => 'required|integer|min:1|max:120', 'sort_order' => 'required|integer|min:1']);
        $id = $data['id'] ?? null;
        unset($data['id']);
        if ($id) {
            $module->contents()->findOrFail($id)->update($data);
        } else {
            $module->contents()->create($data);
        }

        return back();
    }

    /** Menghapus konten dalam modul draft untuk menjaga konsistensi materi terbit. */
    public function deleteContent(Module $module, int $content): RedirectResponse
    {
        abort_if($module->is_published, 422, 'Jadikan modul draft terlebih dahulu.');
        $module->contents()->findOrFail($content)->delete();

        return back();
    }

    /** Menambah atau menyunting soal hanya pada modul draft tanpa riwayat pengerjaan. */
    public function quiz(Request $request, Module $module): RedirectResponse
    {
        abort_if($module->is_published || $module->quizzes()->whereHas('attempts')->exists(), 422, 'Soal terbit atau sudah dikerjakan tidak boleh diubah.');
        $data = $request->validate(['id' => 'nullable|integer', 'question' => 'required|string|max:5000', 'options' => 'required|array|min:2|max:10', 'options.*' => 'required|string|max:1000', 'correct_answer' => 'required|string', 'explanation' => 'nullable|string|max:10000']);
        abort_unless(array_key_exists($data['correct_answer'], $data['options']), 422, 'Kunci harus menunjuk opsi valid.');
        $id = $data['id'] ?? null;
        unset($data['id']);
        if ($id) {
            $module->quizzes()->findOrFail($id)->update($data);
        } else {
            $module->quizzes()->create([...$data, 'points_reward' => 10]);
        }

        return back();
    }

    /** Menghapus soal draft yang tidak mempunyai riwayat percobaan. */
    public function deleteQuiz(Module $module, int $quiz): RedirectResponse
    {
        $question = $module->quizzes()->findOrFail($quiz);
        abort_if($module->is_published || $question->attempts()->exists(), 422);
        $question->delete();

        return back();
    }

    /** Memvalidasi metadata modul tanpa menerima poin atau progres pengguna. */
    private function data(Request $request, ?Module $module = null): array
    {
        $data = $request->validate(['title' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', Rule::unique('modules')->ignore($module)],
            'description' => 'required|string|max:10000', 'iso_control_ref' => 'nullable|string|max:100',
            'order_level' => ['required', 'integer', 'min:1', Rule::unique('modules')->ignore($module)],
            'passing_score' => 'required|integer|min:70|max:100', 'points_reward' => 'required|integer|min:0|max:1000',
            'is_published' => 'required|boolean', 'is_required' => 'required|boolean']);
        if (! $module) {
            $data['is_published'] = false;
        }

        return $data;
    }
}
