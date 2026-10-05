<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AutoEcoleController;
use App\Http\Controllers\Api\LearnerDashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TrafficSignController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login-by-code', [AuthController::class, 'loginByCode']);
Route::get('/auto-ecoles', [AutoEcoleController::class, 'index']);
Route::post('/traffic-signs/analyze', [TrafficSignController::class, 'analyze'])->name('api.traffic-signs.analyze');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Profil & Sécurité
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('api.profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('api.profile.password');

    // Endpoints Espace Apprenant Mobile
    Route::get('/mobile/dashboard', [LearnerDashboardController::class, 'dashboard'])->name('api.mobile.dashboard');
    Route::get('/learner/dashboard', [LearnerDashboardController::class, 'dashboard'])->name('api.learner.dashboard');
    Route::get('/courses', [LearnerDashboardController::class, 'courses'])->name('api.courses.index');
});

