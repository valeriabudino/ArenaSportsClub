<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Console\Command;

class DeleteDemoData extends Command
{
    protected $signature = 'demo:borrar {--force : No pedir confirmacion}';

    protected $description = 'Borra los datos cargados por DemoDataSeeder (clientes ficticios y sus reservas, pagos y comentarios)';

    public function handle(): int
    {
        $users = User::where('email', 'like', '%@' . DemoDataSeeder::EMAIL_DOMAIN);
        $count = $users->count();

        if ($count === 0) {
            $this->info('No hay datos de demostración cargados.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Se van a borrar {$count} clientes de demostración con sus reservas, pagos y comentarios. ¿Continuar?")) {
            return self::FAILURE;
        }

        // Turnos, pagos y comentarios se borran en cascada con el usuario.
        $users->delete();

        $this->info("Datos de demostración borrados ({$count} clientes).");

        return self::SUCCESS;
    }
}
