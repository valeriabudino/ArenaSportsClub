<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('turnos:recordatorio')->hourly();
Schedule::command('accesos:sincronizar')->hourly();

// PENDIENTE PARA EL DEPLOY: 'turns:generate' no esta agendado. Correrlo
// a mano al menos una vez en el hosting (php artisan turns:generate), o
// descomentar la linea de abajo. Sin esto, la ventana de turnos
// disponibles no se extiende sola y despues de --days=30 dejan de
// aparecer turnos para reservar. Requiere que el cron del scheduler
// (php artisan schedule:run cada minuto) este configurado en el hosting.
// Schedule::command('turns:generate')->dailyAt('03:00');
