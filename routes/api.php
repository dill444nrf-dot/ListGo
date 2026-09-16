<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

// ==========================================
// ROUTE PUBLIC (Bisa diakses tanpa login)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 👈 Pindahkan register admin ke sini jika ingin bisa diakses tanpa token terlebih dahulu
Route::post('/admin/register-admin', [AuthController::class, 'registerAdmin']);


// ==========================================
// ROUTE SANCTUM (Membutuhkan Login / Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Route Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Route Tasks
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    // Route Categories
    Route::apiResource('categories', CategoryController::class);

    // Route Reminders
    Route::apiResource('reminders', ReminderController::class);
});