<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AutoEcoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auto-ecoles', [AutoEcoleController::class, 'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
