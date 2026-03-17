<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;

// Guest Dashboard
Route::get('/', function () {
    return Inertia::render('client/Dashboard');
});

// Auth Routes
// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Logout
Route::middleware('auth')->group(function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Client Routes
// Client Dashboard (After Authentication)
Route::get('/', [DashboardController::class, 'show'])->name('dashboard');

// Admin Routes
Route::get('/admin/{any?}', [AdminController::class, 'show'])
    ->where('any', '.*')
    ->name('admin');