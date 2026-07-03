<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

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

    Volt::route('sports', 'admin.sports.index')->name('admin.sports');
    Volt::route('courts', 'admin.courts.index')->name('admin.courts');
});

require __DIR__.'/auth.php';
