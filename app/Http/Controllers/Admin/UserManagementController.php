<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/** Mengelola akun melalui rute admin; mencegah operator menghapus akses dirinya sendiri. */
class UserManagementController extends Controller
{
    /** Menampilkan akun dengan pagination tanpa atribut rahasia. */
    public function index(): Response
    {
        return Inertia::render('Admin/Users', ['users' => User::orderBy('id')->paginate(20)]);
    }

    /** Membuat akun dari input tervalidasi dan password yang di-hash model. */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        User::forceCreate($data);

        return back();
    }

    /** Memperbarui akun tanpa mengubah password jika field dibiarkan kosong. */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if ($user->id === $request->user()->id) {
            abort_unless($data['role'] === 'admin' && $data['is_active'], 422, 'Tidak boleh menonaktifkan akses sendiri.');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->forceFill($data)->save();

        return back();
    }

    /** Menghapus akun selain operator sendiri; data terkait mengikuti aturan FK. */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 422, 'Tidak boleh menghapus akun sendiri.');
        $user->delete();

        return back();
    }

    /** Mengembalikan whitelist data akun; poin dan progres tidak bisa disunting melalui form ini. */
    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255', 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'division' => 'required|string|max:100', 'job_title' => 'nullable|string|max:100',
            'role' => ['required', Rule::in(['user', 'admin'])], 'is_active' => 'required|boolean',
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:12', 'max:255'],
        ]);
    }
}
