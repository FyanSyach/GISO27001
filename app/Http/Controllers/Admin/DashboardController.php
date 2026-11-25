<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use App\Models\UserProgress;
use Inertia\Inertia;
use Inertia\Response;

/** Menampilkan ringkasan analitik yang hanya tersedia bagi administrator. */
class DashboardController extends Controller
{
    /** Mengagregasi akun, penyelesaian dan materi tanpa mengekspos password. */
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', ['stats' => [
            'users' => User::count(), 'active_users' => User::where('is_active', true)->count(),
            'modules' => Module::count(), 'completions' => UserProgress::where('is_completed', true)->count(),
        ]]);
    }
}
