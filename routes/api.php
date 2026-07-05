<?php

use App\Http\Controllers\Api\ReservationValidationController;
use App\Http\Controllers\MercadoPagoController;
use Illuminate\Support\Facades\Route;

Route::get('/reservations/validate', [ReservationValidationController::class, 'validate'])
    ->name('api.reservations.validate');

Route::post('/webhooks/mercadopago', [MercadoPagoController::class, 'webhook'])
    ->name('webhooks.mercadopago');
