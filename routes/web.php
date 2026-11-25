<?php

use App\Http\Controllers\LearningModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/modules', [LearningModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/{module:slug}', [LearningModuleController::class, 'show'])->name('modules.show');
    Route::post('/modules/{module:slug}/quiz', [QuizController::class, 'store'])->middleware('throttle:10,1')->name('modules.quiz.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'role:admin'])->name('admin.dashboard');

require __DIR__.'/auth.php';
