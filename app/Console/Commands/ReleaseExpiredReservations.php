<?php

namespace App\Console\Commands;

use App\Models\Turn;
use Illuminate\Console\Command;

class ReleaseExpiredReservations extends Command
{
    protected $signature = 'turnos:liberar-vencidos';

    protected $description = 'Libera los turnos cuyo plazo para pagar vencio';

    /**
     * La app ya libera los vencidos antes de mostrar o reservar turnos; este
     * comando es para dejar la base ordenada desde el scheduler del hosting.
     */
    public function handle(): void
    {
        $released = Turn::releaseExpiredReservations();

        $this->info("Turnos liberados: {$released}");
    }
}
