<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'pages.home')->name('home');
Route::view('/canchas', 'pages.courts')->name('courts.index');
Route::get('/canchas/{court}', function (App\Models\Court $court) {
    return view('pages.court-detail', compact('court'));
})->name('courts.show');

Route::view('/reservas', 'pages.reservations')->middleware(['auth'])->name('reservations.index');
Route::view('/torneos', 'pages.tournaments')->name('tournaments.index');
Route::view('/nosotros', 'pages.about')->name('about');
Route::view('/contacto', 'pages.contact')->name('contact');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', function () {
        return view('pages.admin.dashboard');
    })->name('admin.dashboard');
    Volt::route('sports', 'admin.sports.index')->name('admin.sports');
    Volt::route('courts', 'admin.courts.index')->name('admin.courts');
    Volt::route('club', 'admin.club.index')->name('admin.club');
    Volt::route('turns', 'admin.turns.index')->name('admin.turns.index');
});

require __DIR__ . '/auth.php';
