<?php

namespace App\Console\Commands;

use App\Models\AccessLog;
use App\Services\CanchasAccessLogClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncAccessLogs extends Command
{
    protected $signature = 'accesos:sincronizar {--date= : Fecha a sincronizar (Y-m-d), por defecto hoy}';

    protected $description = 'Sincroniza el reporte diario de accesos por QR desde la API del control de accesos fisico';

    public function handle(CanchasAccessLogClient $client): int
    {
        $date = $this->option('date');

        try {
            $report = $client->fetch($date);
        } catch (\Throwable $e) {
            Log::warning('No se pudo sincronizar el reporte de accesos', ['date' => $date, 'error' => $e->getMessage()]);
            $this->error('No se pudo obtener el reporte de accesos.');

            return self::FAILURE;
        }

        $synced = 0;

        foreach ($report['logs'] ?? [] as $log) {
            AccessLog::firstOrCreate(
                [
                    'qr_code' => $log['qr_code'],
                    'scanned_at' => $log['timestamp'],
                ],
                [
                    'date' => $report['date'],
                    'status' => $log['status'],
                    'details' => $log['details'] ?? null,
                ]
            );
            $synced++;
        }

        $this->info("Reporte del {$report['date']}: {$synced} escaneos sincronizados.");

        return self::SUCCESS;
    }
}
