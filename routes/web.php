<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/user/dashboard', [UserBookingController::class, 'index'])->name('user.dashboard');
    Route::post('/user/bookings', [UserBookingController::class, 'store'])->name('user.bookings.store');
    Route::patch('/user/bookings/{id}/cancel', [UserBookingController::class, 'cancel'])->name('user.bookings.cancel');
    Route::put('/user/profile', [UserBookingController::class, 'updateProfile'])->name('user.profile.update');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::put('/bookings/{id}', [AdminDashboardController::class, 'updateBooking'])->name('bookings.update');
    Route::delete('/bookings/{id}', [AdminDashboardController::class, 'destroyBooking'])->name('bookings.destroy');

    Route::post('/rooms', [AdminDashboardController::class, 'storeRoom'])->name('rooms.store');
    Route::delete('/rooms/{id}', [AdminDashboardController::class, 'destroyRoom'])->name('rooms.destroy');

    Route::post('/vehicles', [AdminDashboardController::class, 'storeVehicle'])->name('vehicles.store');
    Route::delete('/vehicles/{id}', [AdminDashboardController::class, 'destroyVehicle'])->name('vehicles.destroy');

    Route::post('/agendas', [AdminDashboardController::class, 'storeAgenda'])->name('agendas.store');
    Route::delete('/agendas/{id}', [AdminDashboardController::class, 'destroyAgenda'])->name('agendas.destroy');

    Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::delete('/users/{id}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy');
});