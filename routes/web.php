<?php

use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\TurnQrController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'pages.home')->name('home');
Route::view('/canchas', 'pages.courts')->name('courts.index');
Route::get('/canchas/{court}', function (App\Models\Court $court) {
    return view('pages.court-detail', compact('court'));
})->name('courts.show');

Route::view('/reservas', 'pages.reservations')->middleware(['auth'])->name('reservations.index');

Route::get('/mis-reservas/{turn}/qr', [TurnQrController::class, 'show'])
    ->middleware(['auth'])
    ->name('reservations.qr');

Route::get('/mis-reservas/{turn}/qr/descargar', [TurnQrController::class, 'download'])
    ->middleware(['auth'])
    ->name('reservations.qr.download');

// Sin sesion, protegida por firma temporal (usada como mediaUrl en recordatorios de WhatsApp).
Route::get('/mis-reservas/{turn}/qr-firmado', [TurnQrController::class, 'showSigned'])
    ->middleware(['signed'])
    ->name('reservations.qr.signed');

Route::middleware(['auth'])->group(function () {
    Route::get('/pago/exito', [MercadoPagoController::class, 'success'])->name('payments.success');
    Route::get('/pago/pendiente', [MercadoPagoController::class, 'pending'])->name('payments.pending');
    Route::get('/pago/fallido', [MercadoPagoController::class, 'failure'])->name('payments.failure');
});
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
    Volt::route('refunds', 'admin.refunds.index')->name('admin.refunds');
    Volt::route('accesos', 'admin.access-logs.index')->name('admin.access-logs.index');
});

require __DIR__ . '/auth.php';
