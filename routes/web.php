<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModuleManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\LearningModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/certificate/verify/{certificateNumber}', [GamificationController::class, 'verify'])->name('certificate.verify');

Route::middleware('auth')->group(function () {
    Route::get('/achievements', [GamificationController::class, 'index'])->name('achievements.index');
    Route::get('/certificate', [GamificationController::class, 'certificate'])->middleware('throttle:5,1')->name('certificate.download');
    Route::get('/modules', [LearningModuleController::class, 'index'])->name('modules.index');
    Route::get('/modules/{module:slug}', [LearningModuleController::class, 'show'])->name('modules.show');
    Route::post('/modules/{module:slug}/quiz', [QuizController::class, 'store'])->middleware('throttle:10,1')->name('modules.quiz.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('users', UserManagementController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('modules', ModuleManagementController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('modules/{module}/contents', [ModuleManagementController::class, 'content'])->name('contents.store');
    Route::delete('modules/{module}/contents/{content}', [ModuleManagementController::class, 'deleteContent'])->name('contents.destroy');
    Route::post('modules/{module}/quizzes', [ModuleManagementController::class, 'quiz'])->name('quizzes.store');
    Route::delete('modules/{module}/quizzes/{quiz}', [ModuleManagementController::class, 'deleteQuiz'])->name('quizzes.destroy');
    Route::get('reports/excel', [ReportController::class, 'excel'])->middleware('throttle:5,1')->name('reports.excel');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->middleware('throttle:5,1')->name('reports.pdf');
});

Route::middleware(['auth', 'throttle:60,1'])->prefix('forum')->name('forum.')->group(function (): void {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::post('/', [ForumController::class, 'store'])->name('store');
    Route::get('/{thread}', [ForumController::class, 'show'])->name('show');
    Route::patch('/{thread}', [ForumController::class, 'update'])->name('update');
    Route::delete('/{thread}', [ForumController::class, 'destroy'])->name('destroy');
    Route::post('/{thread}/replies', [ForumController::class, 'reply'])->name('replies.store');
    Route::patch('/replies/{reply}', [ForumController::class, 'updateReply'])->name('replies.update');
    Route::delete('/replies/{reply}', [ForumController::class, 'deleteReply'])->name('replies.destroy');
    Route::post('/{thread}/solution', [ForumController::class, 'solution'])->name('solution');
});

require __DIR__.'/auth.php';
