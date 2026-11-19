<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Sito pubblico
Route::get('/', [BookingController::class, 'home'])->name('home');
Route::get('/prenota/{service}', [BookingController::class, 'create'])->name('booking.create');
Route::get('/prenota/{service}/orari', [BookingController::class, 'times'])->name('booking.times');
Route::post('/prenota/{service}', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
Route::get('/prenotazione/{booking}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/prenotazione/{booking}/annulla', [BookingController::class, 'cancel'])->name('booking.cancel');

// Area riservata
Route::get('/admin/login', [Admin\AuthController::class, 'show'])->middleware('guest')->name('login');
Route::post('/admin/login', [Admin\AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1']);

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/prenotazioni/{booking}', [Admin\DashboardController::class, 'updateStatus'])->name('bookings.status');
    Route::resource('servizi', Admin\ServiceController::class)->except('show')->parameters(['servizi' => 'service'])->names('services');
    Route::get('/chiusure', [Admin\ClosureController::class, 'index'])->name('closures.index');
    Route::post('/chiusure', [Admin\ClosureController::class, 'store'])->name('closures.store');
    Route::delete('/chiusure/{closure}', [Admin\ClosureController::class, 'destroy'])->name('closures.destroy');
});
