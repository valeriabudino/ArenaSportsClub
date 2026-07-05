<?php

use App\Http\Controllers\MercadoPagoController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/mercadopago', [MercadoPagoController::class, 'webhook'])
    ->name('webhooks.mercadopago');
