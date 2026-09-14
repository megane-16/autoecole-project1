<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
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
    ->name('admin.preview');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, 'role:auto_ecole,administrateur'])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::post('dashboard/learners', [AuthController::class, 'storeLearner'])->name('dashboard.learners.store');

        Route::get('demandes', [RegistrationRequestController::class, 'index'])->name('demandes.index');
        Route::get('demandes/{registration_request}', [RegistrationRequestController::class, 'show'])->name('demandes.show');
        Route::post('demandes/{registration_request}/validate', [RegistrationRequestController::class, 'validateRequest'])->name('demandes.validate');
        Route::delete('demandes/{registration_request}', [RegistrationRequestController::class, 'destroy'])->name('demandes.destroy');
    });

require __DIR__.'/settings.php';
