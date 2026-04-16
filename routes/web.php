<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Client Routes
// Client Dashboard (After Authentication)
Route::get('/', [DashboardController::class, 'show'])->name('dashboard');

// ...
Route::middleware(['auth'])->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'clientBookings'])->name('client.bookings');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

// Admin Routes (Protected by admin middleware)
Route::middleware('admin')->group(function () {
    // Specific routes
    Route::get('/admin/guests', [AdminController::class, 'guests']);

    // Admin - Booking Routes
    Route::prefix('admin/bookings')->group(function () {
        // 1. Route for updating status (Confirm/Cancel)
        Route::patch('/{booking}/status', [BookingController::class, 'updateStatus']);

        // 2. Route for the Edit Modal (Save Changes)
        Route::put('/{booking}', [BookingController::class, 'update']);

        // 3. Route for Deleting
        Route::delete('/{booking}', [BookingController::class, 'destroy']);
    });

    // Admin - Guests Routes
    Route::post('/admin/guests', [GuestController::class, 'store'])->name('admin.guests.store');
    Route::delete('/admin/guests/{guest}', [GuestController::class, 'destroy'])->name('admin.guests.destroy');
    Route::put('/admin/guests/{guest}', [GuestController::class, 'update'])->name('admin.guests.update');
    Route::patch('/admin/guests/{guest}/blacklist', [GuestController::class, 'blacklist'])->name('admin.guest.update');

    // Admin - Room Routes
    Route::resource('rooms', RoomController::class);
    Route::post('/rooms/{room}/status', [RoomController::class, 'setStatus'])->name('rooms.setStatus');

    // Wildcard
    Route::get('/admin/{any?}', [AdminController::class, 'show'])
        ->where('any', '.*')
        ->name('admin');
});
