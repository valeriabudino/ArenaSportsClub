<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/canchas', 'pages.courts')->name('courts.index');
Route::view('/reservas', 'pages.reservations')->name('reservations.index');
Route::view('/torneos', 'pages.tournaments')->name('tournaments.index');
Route::view('/nosotros', 'pages.about')->name('about');
Route::view('/contacto', 'pages.contact')->name('contact');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', function () {
        return view('pages.admin.dashboard');
    })->name('admin.dashboard');
});

require __DIR__.'/auth.php';
