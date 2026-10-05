<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Moniteur\LessonController;
use App\Http\Controllers\Moniteur\ScheduleController;
use App\Http\Controllers\Moniteur\StudentController;
use App\Http\Controllers\PublicApplicationController;
use App\Http\Controllers\RegistrationRequestController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicApplicationController::class, 'home'])->name('home');

// Routes pour postuler (Web)
Route::get('postuler/{team:slug}', [PublicApplicationController::class, 'show'])->name('postuler.show');
Route::post('postuler/{team:slug}', [PublicApplicationController::class, 'store'])->name('postuler.store');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('admin', AdminDashboardController::class)
    ->middleware(['auth', 'verified', EnsureUserRole::class.':administrateur'])
    ->name('admin.dashboard');

Route::get('admin/preview', [AdminDashboardController::class, 'preview'])
    ->middleware(['auth', 'verified', EnsureUserRole::class.':administrateur'])
    ->name('admin.preview');


use App\Http\Controllers\AutoEcoleDashboardController;

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, 'role:auto_ecole,administrateur,moniteur'])
    ->group(function () {
        Route::get('dashboard', [AutoEcoleDashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/learners', [AuthController::class, 'storeLearner'])->name('dashboard.learners.store');
        Route::post('dashboard/lessons', [AutoEcoleDashboardController::class, 'storeLesson'])->name('dashboard.lessons.store');


        Route::get('demandes', [RegistrationRequestController::class, 'index'])->name('demandes.index');
        Route::get('demandes/{registration_request}', [RegistrationRequestController::class, 'show'])->name('demandes.show');
        Route::post('demandes/{registration_request}/validate', [RegistrationRequestController::class, 'validateRequest'])->name('demandes.validate');
        Route::post('demandes/{registration_request}/reject', [RegistrationRequestController::class, 'rejectRequest'])->name('demandes.reject');
        Route::delete('demandes/{registration_request}', [RegistrationRequestController::class, 'destroy'])->name('demandes.destroy');

        // Espace Moniteur (Planning, Apprenants, CRUD Cours)
        Route::prefix('moniteur')->as('instructor.')->group(function () {
            // Planning
            Route::get('planning', [ScheduleController::class, 'index'])->name('schedule.index');

            // Apprenants
            Route::get('apprenants', [StudentController::class, 'index'])->name('students.index');
            Route::get('apprenants/{student}', [StudentController::class, 'show'])->name('students.show');
            Route::post('apprenants/assign', [StudentController::class, 'assign'])->name('students.assign');
            Route::delete('apprenants/{student}/unassign', [StudentController::class, 'unassign'])->name('students.unassign');

            // CRUD & Export Cours
            Route::get('cours/export', [LessonController::class, 'export'])->name('lessons.export');
            Route::get('cours/{lesson}/download', [LessonController::class, 'downloadSheet'])->name('lessons.download');
            Route::patch('cours/{lesson}/status', [LessonController::class, 'updateStatus'])->name('lessons.status');
            Route::resource('cours', LessonController::class, ['parameters' => ['cours' => 'lesson']])->names('lessons');
        });
    });

require __DIR__.'/settings.php';

