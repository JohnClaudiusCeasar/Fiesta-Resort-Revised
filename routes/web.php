<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;

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
// Specific routes
Route::get('/admin/guests', [AdminController::class, 'guests']);

// Admin - Guests Routes
Route::post('/admin/guests'                    , [GuestController::class, 'store'])->name('admin.guests.store');
Route::delete('/admin/guests/{guest}'          , [GuestController::class, 'destroy'])->name('admin.guests.destroy');
Route::put('/admin/guests/{guest}'             , [GuestController::class, 'update'])->name('admin.guests.update');
Route::patch('/admin/guests/{guest}/blacklist' , [GuestController::class, 'blacklist'])->name('admin.guest.update');

// Admin - Room Routes
Route::resource('rooms', RoomController::class);
Route::post('/rooms/{room}/toggle-availability', [RoomController::class, 'toggleAvailability'])->name('rooms.toggleAvailibility');

// Wildcard
Route::get('/admin/{any?}', [AdminController::class, 'show'])
    ->where('any', '.*')
    ->name('admin');