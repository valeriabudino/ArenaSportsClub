<?php

use App\Http\Controllers\Api\ReservationValidationController;
use Illuminate\Support\Facades\Route;

Route::get('/reservations/validate', [ReservationValidationController::class, 'validate'])
    ->name('api.reservations.validate');
